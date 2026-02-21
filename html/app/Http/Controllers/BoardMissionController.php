<?php

namespace App\Http\Controllers;

use App\Models\BoardMission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BoardMissionController extends AbstractBoardController
{
    
    protected $modelClass = BoardMission::class;
    protected $viewPath = 'board_mission';
    protected $routePrefix = 'board-mission';
    protected $uploadPath = 'uploads/board_mission';

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    protected function getItemsPerPage()
    {
        return 15;
    }
    
    protected function useHardDelete()
    {
        return true;
    }

    /**
     * 게시글 목록 조회 (관리자는 전체, 일반 회원은 본인 글만)
     */
    public function index(Request $request)
    {
        $model = app($this->modelClass);
        $query = $model::with('user')->where('mq_status', 1);
        
        // 관리자(level 10)가 아니면 본인 글만 조회
        $user = Auth::user();
        if ($user->mq_level < 10) {
            $query->where('mq_user_id', $user->mq_user_id);
        }
        
        // 검색 처리
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('mq_title', 'like', '%'.$searchTerm.'%')
                  ->orWhere('mq_content', 'like', '%'.$searchTerm.'%');
            });
        }
        
        // 게시판별 카테고리 가져오기
        $boardType = $this->getBoardTypeFromModelClass();
        $categories = $this->getCategories($boardType);
        
        // 카테고리 필터
        if ($request->has('category') && $request->category != '') {
            $query->where('mq_category', $request->category);
        }
        
        // 정렬
        switch ($request->get('sort', 'latest')) {
            case 'views':
                $query->orderBy('mq_view_cnt', 'desc');
                break;
            case 'likes':
                $query->orderBy('mq_like_cnt', 'desc');
                break;
            default:
                $query->orderBy('mq_reg_date', 'desc');
        }
        
        $posts = $query->paginate($this->getItemsPerPage());
        
        // 이미지 처리
        $this->processListImages($posts);
        
        // 게시판별 적절한 카테고리 색상 선택
        $categoryColors = $this->getCategoryColors($boardType);
        
        return view($this->viewPath.'.index', [
            'posts' => $posts,
            'categories' => $categories,
            'categoryColors' => $categoryColors,
        ]);
    }

    /**
     * 게시글 상세 보기 (관리자는 전체, 일반 회원은 본인 글만)
     */
    public function show($idx)
    {
        $model = app($this->modelClass);
        $post = $model::where('mq_status', 1)->findOrFail($idx);
        
        // 관리자(level 10)가 아니고, 본인 글이 아니면 접근 차단
        $user = Auth::user();
        if ($user->mq_level < 10 && $post->mq_user_id !== $user->mq_user_id) {
            return redirect()->route($this->routePrefix.'.index')
                ->with('error', '본인이 작성한 글만 열람할 수 있습니다.');
        }
        
        // 이미지 처리
        $this->processPostImages($post);
        
        // 조회수 증가 (세션을 통한 중복 증가 방지)
        if (!session()->has('viewed_post_' . $idx)) {
            $post->increment('mq_view_cnt');
            session(['viewed_post_' . $idx => true]);
        }

        // 게시판별 적절한 카테고리 색상 선택
        $boardType = $this->getBoardTypeFromModelClass();

        $isLiked = false;
        if (auth()->check()) {
            $isLiked = \DB::table('mq_like_history')
                ->where('mq_user_id', auth()->user()->mq_user_id)
                ->where('mq_board_name', $this->getBoardTypeFromModelClass())
                ->where('mq_board_idx', $idx)
                ->exists();
        }

        return view($this->viewPath.'.show', [
            'post' => $post,
            'categoryColors' => $this->getCategoryColors($boardType),
            'isLiked' => $isLiked
        ]);
    }

    /**
     * 게시글 수정 폼 (관리자 또는 본인만)
     */
    public function edit($idx)
    {
        $model = app($this->modelClass);
        $post = $model::findOrFail($idx);
        
        // 권한 검사
        if (!$this->canEdit($post)) {
            return redirect()->route($this->routePrefix.'.show', $idx)
                ->with('error', '수정 권한이 없습니다.');
        }

        // 게시판별 카테고리 가져오기
        $boardType = $this->getBoardTypeFromModelClass();
        $categories = $this->getCategories($boardType);

        return view($this->viewPath.'.edit', [
            'post' => $post,
            'categories' => $categories
        ]);
    }

    /**
     * 권한 - 게시글 수정 가능 여부 (관리자 또는 작성자)
     */
    protected function canEdit($post)
    {
        $user = Auth::user();
        return $user->mq_level >= 10 || $post->mq_user_id === $user->mq_user_id;
    }

    /**
     * 권한 - 게시글 삭제 가능 여부 (관리자 또는 작성자)
     */
    protected function canDelete($post)
    {
        $user = Auth::user();
        return $user->mq_level >= 10 || $post->mq_user_id === $user->mq_user_id;
    }

    public function create()
    {
        $categories = $this->getBoardMissionCategories();
        
        return view($this->viewPath.'.create', [
            'categories' => $categories
        ]);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            
            $request->validate([
                'upload' => 'required|image|max:2048'
            ]);
            
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            $randomName = Str::random(32) . '.' . $extension;
            
            $path = $file->storeAs($this->uploadPath.'/editor', $randomName, 'public');
            
            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }
        
        return response()->json([
            'error' => [
                'message' => '이미지 업로드에 실패했습니다.'
            ]
        ], 400);
    }
}
