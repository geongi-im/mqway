<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoardController extends Controller
{
    protected $categories = ['경제용어', 'RS랭킹', '순매수대금', '거래량'];
    protected $categoryColors = [
        '경제용어' => 'bg-yellow-100 text-yellow-800',
        'RS랭킹' => 'bg-blue-100 text-blue-800',
        '순매수대금' => 'bg-green-100 text-green-800',
        '거래량' => 'bg-red-100 text-red-800'
    ];

    /**
     * 게시글 목록
     */
    public function index(Request $request)
    {
        $query = Board::query();

        // 검색어 처리
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('mq_title', 'like', "%{$search}%")
                  ->orWhere('mq_content', 'like', "%{$search}%");
            });
        }

        // 카테고리 필터
        if ($request->has('category') && $request->get('category')) {
            $query->where('mq_category', $request->get('category'));
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

        $posts = $query->paginate(10);
        
        // 이미지 경로 처리
        foreach ($posts as $post) {
            // 이미지 경로가 이미 URL 형식인지 확인
            if ($post->mq_image && !filter_var($post->mq_image, FILTER_VALIDATE_URL)) {
                $post->mq_image = asset('storage/' . $post->mq_image);
            } elseif (!$post->mq_image) {
                $post->mq_image = asset('images/content/no_image.jpeg');
            }
        }
        
        return view('board.index', [
            'posts' => $posts,
            'categories' => $this->categories,
            'categoryColors' => $this->categoryColors,
        ]);
    }

    /**
     * 게시글 작성 폼
     */
    public function create()
    {
        return view('board.create', [
            'categories' => $this->categories
        ]);
    }

    /**
     * 게시글 저장
     */
    public function store(Request $request)
    {
        $request->validate([
            'mq_title' => 'required|max:255',
            'mq_content' => 'required',
            'mq_category' => 'required|in:' . implode(',', $this->categories),
            'mq_image' => 'nullable|image|max:2048'
        ]);

        $board = new Board();
        $board->mq_title = $request->mq_title;
        $board->mq_content = $request->mq_content;
        $board->mq_category = $request->mq_category;
        $board->mq_writer = Auth::user()->mq_user_id;
        $board->mq_view_cnt = 0;
        $board->mq_like_cnt = 0;
        $board->mq_status = 1;
        
        // 이미지 처리
        if ($request->hasFile('mq_image')) {
            $file = $request->file('mq_image');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // 난수화된 파일명 생성 (32자 랜덤 문자열 + 확장자)
            $randomName = Str::random(32) . '.' . $extension;
            
            // uploads/board 디렉토리에 저장
            $path = $file->storeAs('uploads/board', $randomName, 'public');
            
            $board->mq_image = $path;
            $board->mq_original_image = $originalName;
        }

        $board->mq_reg_date = now();
        $board->save();

        return redirect()->route('board.show', $board->idx)->with('success', '게시글이 작성되었습니다.');
    }

    /**
     * 게시글 상세 보기
     */
    public function show($idx)
    {
        $post = Board::findOrFail($idx);
        
        // 이미지 경로 처리
        $post->mq_image = $post->mq_image 
            ? asset('storage/' . $post->mq_image)
            : asset('images/content/no_image.jpeg');
        
        // 조회수 증가
        $post->increment('mq_view_cnt');

        return view('board.show', [
            'post' => $post,
            'categoryColors' => $this->categoryColors,
        ]);
    }

    /**
     * 게시글 수정 폼
     */
    public function edit($idx)
    {
        $post = Board::findOrFail($idx);
        $categories = ['테크', '경제', '산업', '증권'];
        
        // 작성자 체크
        if ($post->mq_writer !== Auth::user()->mq_user_id) {
            return redirect()->route('board.show', $idx)->with('error', '수정 권한이 없습니다.');
        }

        return view('board.edit', [
            'post' => $post,
            'categories' => $categories
        ]);
    }

    /**
     * 게시글 업데이트
     */
    public function update(Request $request, $idx)
    {
        $request->validate([
            'mq_title' => 'required|max:255',
            'mq_content' => 'required',
            'mq_category' => 'required|in:테크,경제,사회,문화,스포츠',
            'mq_image' => 'nullable|image|max:2048'
        ]);

        $board = Board::findOrFail($idx);
        
        // 작성자 체크
        if ($board->mq_writer !== Auth::user()->mq_user_id) {
            return redirect()->route('board.show', $idx)->with('error', '수정 권한이 없습니다.');
        }

        $board->mq_title = $request->mq_title;
        $board->mq_content = $request->mq_content;
        $board->mq_category = $request->mq_category;
        
        // 이미지 처리
        if ($request->hasFile('mq_image')) {
            // 기존 이미지 삭제
            if ($board->mq_image) {
                Storage::disk('public')->delete($board->mq_image);
            }
            
            $path = $request->file('mq_image')->store('board', 'public');
            $board->mq_image = $path;
        }

        $board->mq_update_date = now();
        $board->save();

        return redirect()->route('board.show', $board->idx)->with('success', '게시글이 수정되었습니다.');
    }

    /**
     * 게시글 삭제 (소프트 삭제)
     */
    public function destroy($idx)
    {
        $board = Board::findOrFail($idx);
        
        // 작성자 체크
        if ($board->mq_writer !== Auth::user()->mq_user_id) {
            return redirect()->route('board.show', $idx)->with('error', '삭제 권한이 없습니다.');
        }

        $board->mq_status = 0;  // 소프트 삭제
        $board->save();

        return redirect()->route('board.index')->with('success', '게시글이 삭제되었습니다.');
    }

    /**
     * 좋아요 기능
     */
    public function like($idx)
    {
        try {
            $board = Board::findOrFail($idx);
            $board->increment('mq_like_cnt');
            
            return response()->json([
                'success' => true,
                'likes' => $board->fresh()->mq_like_cnt
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '좋아요 처리 중 오류가 발생했습니다.'
            ], 500);
        }
    }

    /**
     * CKEditor 이미지 업로드 처리
     */
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            
            // 파일 유효성 검사
            $request->validate([
                'upload' => 'required|image|max:2048'
            ]);
            
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // 난수화된 파일명 생성 (32자 랜덤 문자열 + 확장자)
            $randomName = Str::random(32) . '.' . $extension;
            
            // uploads/editor 디렉토리에 저장
            $path = $file->storeAs('uploads/editor', $randomName, 'public');
            
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