<?php

namespace App\Http\Controllers;

use App\Models\BoardPortfolio;
use App\Models\InvestorPortfolio;
use App\Models\InvestorPortfolioDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\BoardCategoryColorTrait;

class BoardPortfolioController extends AbstractBoardController
{
    use BoardCategoryColorTrait;
    
    protected $modelClass = BoardPortfolio::class;
    protected $viewPath = 'board_portfolio';
    protected $routePrefix = 'board-portfolio';
    
    /**
     * 생성자 - 회원 인증 미들웨어 적용 (index, show 제외)
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }
    
    /**
     * 페이지당 게시글 수 설정
     */
    protected function getItemsPerPage()
    {
        return 15;
    }
    
    /**
     * 하드 삭제 사용 여부 설정
     */
    protected function useHardDelete()
    {
        return true;
    }
    
    /**
     * 게시글 작성 폼 표시 - text 입력 필드로 변경
     */
    public function create()
    {
        // 별도의 카테고리 목록이 필요없으므로 빈 배열 전달
        return view($this->viewPath.'.create');
    }
    
    /**
     * 목록 페이지 표시 - 기본 목록 메서드 오버라이드
     */
    public function index(Request $request)
    {
        $query = $this->modelClass::query();
        
        // 검색 필터 적용
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('mq_title', 'like', "%{$search}%")
                ->orWhere('mq_user_id', 'like', "%{$search}%")
                ->orWhere('mq_investor_code', 'like', "%{$search}%");
        }
        
        // 카테고리(investor_code) 필터링
        if ($request->has('category') && $request->input('category') !== 'all') {
            $query->where('mq_investor_code', $request->input('category'));
        }
        
        // 정렬 및 페이징
        $posts = $query->orderBy('idx', 'desc')
                    ->paginate($this->getItemsPerPage());
        
        // 중복 없는 investor_code 목록 가져오기
        $categories = BoardPortfolio::select('mq_investor_code')
                    ->distinct()
                    ->orderBy('mq_investor_code')
                    ->pluck('mq_investor_code')
                    ->toArray();
        
        // 카테고리 색상 매핑 - 커스텀 로직 사용
        $categoryColors = $this->getPortfolioCategoryColors($categories);
        
        return view($this->viewPath.'.index', [
            'posts' => $posts,
            'categories' => $categories,
            'categoryColors' => $categoryColors,
            'currentCategory' => $request->input('category', 'all')
        ]);
    }
    
    /**
     * 상세 페이지 표시 - investor_portfolio 및 investor_portfolio_detail 테이블에서 데이터 가져오기
     */
    public function show($idx)
    {
        $post = $this->modelClass::findOrFail($idx);
        
        // 조회수 증가
        $post->increment('mq_view_cnt');
        
        // 카테고리 색상 매핑 - 커스텀 로직 사용
        $categoryColors = $this->getPortfolioCategoryColors([$post->mq_investor_code]);
        
        // InvestorPortfolio 모델을 통해 포트폴리오 메타 정보 가져오기
        $portfolioMeta = InvestorPortfolio::find($post->mq_portfolio_idx);
            
        if (!$portfolioMeta) {
            return redirect()->route($this->routePrefix.'.index')
                ->with('error', '포트폴리오 정보를 찾을 수 없습니다.');
        }
        
        // InvestorPortfolioDetail 모델을 통해 포트폴리오 상세 종목 정보 가져오기
        $portfolioDetails = InvestorPortfolioDetail::where('p_idx', $post->mq_portfolio_idx)
            ->orderBy('portfolio_rate', 'desc')
            ->get();
            
        // 최대 보유 종목 찾기
        $topHolding = $portfolioDetails->first();
        
        // 차트 데이터 준비 - 모든 종목을 차트에 표시
        $chartData = $portfolioDetails->map(function($item) {
            return [
                'name' => $item->ticker,
                'value' => (float)$item->portfolio_rate
            ];
        });

        $isLiked = false;
        if (auth()->check()) {
            $isLiked = DB::table('mq_like_history')
                ->where('mq_user_id', auth()->user()->mq_user_id)
                ->where('mq_board_name', $this->getBoardTypeFromModelClass())
                ->where('mq_board_idx', $idx)
                ->exists();
        }
        
        return view($this->viewPath.'.show', [
            'post' => $post,
            'categoryColors' => $categoryColors,
            'portfolioMeta' => $portfolioMeta,
            'portfolioDetails' => $portfolioDetails,
            'topHolding' => $topHolding,
            'chartData' => $chartData,
            'isLiked' => $isLiked
        ]);
    }
    
    /**
     * 포트폴리오 카테고리(investor_code)에 대한 색상 매핑
     */
    protected function getPortfolioCategoryColors($categories)
    {
        $colors = [];
        foreach ($categories as $index => $category) {
            if (is_string($category)) {
                $colors[$category] = $this->predefinedColors[$index % count($this->predefinedColors)];
            }
        }
        return $colors;
    }
    
    /**
     * 수정 폼 표시
     */
    public function edit($idx)
    {
        $post = $this->modelClass::findOrFail($idx);
        
        // 본인 게시글만 수정 가능
        if (Auth::user()->mq_user_id !== $post->mq_user_id) {
            return redirect()->route($this->routePrefix.'.show', $idx)
                           ->with('error', '자신의 게시글만 수정할 수 있습니다.');
        }
        
        return view($this->viewPath.'.edit', [
            'post' => $post
        ]);
    }
    
    /**
     * 게시글 저장
     */
    public function store(Request $request)
    {
        // 유효성 검사
        $request->validate([
            'mq_title' => 'required|string|max:255',
            'mq_portfolio_idx' => 'required|numeric',
            'mq_investor_code' => 'required|string|max:50',
        ]);

        // 포트폴리오 존재 여부 확인
        $portfolioExists = InvestorPortfolio::where('idx', $request->mq_portfolio_idx)->exists();
        if (!$portfolioExists) {
            return back()->withInput()
                ->with('error', '입력한 포트폴리오 ID에 해당하는 정보를 찾을 수 없습니다.');
        }

        // 트랜잭션 시작
        DB::beginTransaction();
        
        try {
            // 게시글 생성
            $board = new BoardPortfolio();
            $board->mq_title = $request->mq_title;
            $board->mq_portfolio_idx = $request->mq_portfolio_idx;
            $board->mq_investor_code = $request->mq_investor_code;
            $board->mq_user_id = Auth::user()->mq_user_id;
            $board->mq_view_cnt = 0;
            $board->mq_like_cnt = 0;
            $board->mq_status = 1;
            $board->mq_reg_date = now();
            $board->save();
            
            DB::commit();
            
            return redirect()->route($this->routePrefix.'.show', $board->idx)
                ->with('success', '게시글이 등록되었습니다.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', '저장 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
} 