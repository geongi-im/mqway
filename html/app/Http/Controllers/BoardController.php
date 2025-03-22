<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Traits\BoardCategoryColorTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoardController extends Controller
{
    use BoardCategoryColorTrait;

    /**
     * 게시글 목록
     */
    public function index(Request $request)
    {
        $categories = $this->getCategories();
        $categoryColors = $this->getCategoryColors();

        $query = Board::where('mq_status', 1);

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

        $posts = $query->paginate(12);
        
        // 이미지 경로 처리
        foreach ($posts as $post) {
            if (is_array($post->mq_image) && !empty($post->mq_image)) {
                $filename = $post->mq_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL) 
                    ? asset('storage/uploads/board/' . $filename)
                    : $filename;
            } else {
                $post->mq_image = asset('images/content/no_image.jpeg');
            }
        }
        
        return view('board.index', [
            'posts' => $posts,
            'categories' => $categories,
            'categoryColors' => $categoryColors,
        ]);
    }

    /**
     * 게시글 작성 폼
     */
    public function create()
    {
        // 카테고리 목록을 DB에서 가져오기
        $categories = Board::select('mq_category')
            ->distinct()
            ->orderBy('mq_category')
            ->pluck('mq_category')
            ->toArray();

        return view('board.create', [
            'categories' => $categories
        ]);
    }

    /**
     * 게시글 저장
     */
    public function store(Request $request)
    {
        // 유효한 카테고리 목록 가져오기
        $validCategories = Board::select('mq_category')
            ->distinct()
            ->pluck('mq_category')
            ->toArray();

        $request->validate([
            'mq_title' => 'required|max:255',
            'mq_content' => 'required',
            'mq_category' => 'required|in:' . implode(',', $validCategories),
            'mq_image.*' => 'image|max:2048'
        ]);

        $imagePaths = [];
        $originalNames = [];

        if ($request->hasFile('mq_image')) {
            foreach ($request->file('mq_image') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $randomName = Str::random(32).'.'.$extension;
                $file->storeAs('uploads/board', $randomName, 'public');
                
                $imagePaths[] = $randomName;
                $originalNames[] = $originalName;
            }
        }

        $board = new Board();
        $board->mq_title = $request->mq_title;
        $board->mq_content = $request->mq_content;
        $board->mq_category = $request->mq_category;
        $board->mq_writer = Auth::user()->mq_user_id;
        $board->mq_view_cnt = 0;
        $board->mq_like_cnt = 0;
        $board->mq_status = 1;
        
        $board->mq_image = $imagePaths;
        $board->mq_original_image = $originalNames;

        $board->mq_reg_date = now();
        $board->save();

        return redirect()->route('board.show', $board->idx)->with('success', '게시글이 작성되었습니다.');
    }

    /**
     * 게시글 상세 보기
     */
    public function show($idx)
    {
        $post = Board::where('mq_status', 1)->findOrFail($idx);
        
        // 이미지 배열 처리
        if (is_array($post->mq_image)) {
            $post->mq_image = array_map(function($filename) {
                return asset('storage/uploads/board/' . $filename);
            }, $post->mq_image);
        } else {
            $post->mq_image = [asset('images/content/no_image.jpeg')];
        }
        
        // 조회수 증가
        $post->increment('mq_view_cnt');

        return view('board.show', [
            'post' => $post,
            'categoryColors' => $this->getCategoryColors(),
        ]);
    }

    /**
     * 게시글 수정 폼
     */
    public function edit($idx)
    {
        $post = Board::findOrFail($idx);
        
        // 작성자 체크
        if ($post->mq_writer !== Auth::user()->mq_user_id) {
            return redirect()->route('board.show', $idx)->with('error', '수정 권한이 없습니다.');
        }

        // 카테고리 목록을 DB에서 가져오기
        $categories = Board::select('mq_category')
            ->distinct()
            ->orderBy('mq_category')
            ->pluck('mq_category')
            ->toArray();

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
        // 유효한 카테고리 목록 가져오기
        $validCategories = Board::select('mq_category')
            ->distinct()
            ->pluck('mq_category')
            ->toArray();

        $request->validate([
            'mq_title' => 'required|max:255',
            'mq_content' => 'required',
            'mq_category' => 'required|in:' . implode(',', $validCategories),
            'mq_image.*' => 'nullable|image|max:2048'
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
        $imagePaths = is_array($board->mq_image) ? $board->mq_image : [];
        $originalNames = is_array($board->mq_original_image) ? $board->mq_original_image : [];
        
        if ($request->hasFile('mq_image')) {
            foreach ($request->file('mq_image') as $file) {
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $randomName = Str::random(32).'.'.$extension;
                    $file->storeAs('uploads/board', $randomName, 'public');
                    
                    $imagePaths[] = $randomName;
                    $originalNames[] = $originalName;
                }
            }
            
            $board->mq_image = $imagePaths;
            $board->mq_original_image = $originalNames;
        }

        $board->mq_update_date = now();
        $board->save();

        return redirect()->route('board.show', $board->idx)->with('success', '게시글이 수정되었습니다.');
    }

    /**
     * 게시글 삭제 (하드 삭제)
     */
    public function destroy($idx)
    {
        $board = Board::findOrFail($idx);
        
        // 작성자 체크
        if ($board->mq_writer !== Auth::user()->mq_user_id) {
            return redirect()->route('board.show', $idx)->with('error', '삭제 권한이 없습니다.');
        }

        // 이미지 파일 삭제
        if (is_array($board->mq_image)) {
            foreach ($board->mq_image as $image) {
                Storage::disk('public')->delete('uploads/board/' . $image);
            }
        }

        // 데이터베이스에서 완전히 삭제
        $board->delete();

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

    /**
     * 이미지 삭제 API
     */
    public function deleteImage($idx, $filename)
    {
        try {
            $board = Board::findOrFail($idx);
            
            // 작성자 체크
            if ($board->mq_writer !== Auth::user()->mq_user_id) {
                return response()->json([
                    'success' => false,
                    'message' => '삭제 권한이 없습니다.'
                ], 403);
            }

            // 이미지 배열에서 해당 파일명 찾기
            if (is_array($board->mq_image)) {
                $index = array_search($filename, $board->mq_image);
                if ($index !== false) {
                    // 실제 파일 삭제
                    Storage::disk('public')->delete('uploads/board/' . $filename);
                    
                    // 배열에서 제거
                    $images = $board->mq_image;
                    $originalNames = $board->mq_original_image;
                    unset($images[$index]);
                    unset($originalNames[$index]);
                    
                    // 배열 재정렬
                    $board->mq_image = array_values($images);
                    $board->mq_original_image = array_values($originalNames);
                    $board->save();
                    
                    return response()->json([
                        'success' => true,
                        'message' => '이미지가 삭제되었습니다.'
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => '이미지를 찾을 수 없습니다.'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '이미지 삭제 중 오류가 발생했습니다.'
            ], 500);
        }
    }
} 