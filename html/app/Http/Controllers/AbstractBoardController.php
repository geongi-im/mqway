<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Traits\BoardCategoryColorTrait;

abstract class AbstractBoardController extends Controller
{
    use BoardCategoryColorTrait;
    
    // 하위 클래스에서 정의할 속성들
    protected $modelClass;         // 게시판에 사용할 모델 클래스
    protected $viewPath;           // 뷰 경로 (예: 'board', 'board_research')
    protected $routePrefix;        // 라우트 접두사 (예: 'board', 'board_research')
    protected $uploadPath;         // 업로드 경로 (예: 'uploads/board', 'uploads/board_research')
    
    /**
     * 게시글 목록 조회
     */
    public function index(Request $request)
    {
        $model = app($this->modelClass);
        $query = $model::where('mq_status', 1);
        
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
     * 게시글 작성 폼
     */
    public function create()
    {
        // 게시판별 카테고리 가져오기
        $boardType = $this->getBoardTypeFromModelClass();
        $categories = $this->getCategories($boardType);

        return view($this->viewPath.'.create', [
            'categories' => $categories
        ]);
    }
    
    /**
     * 게시글 저장
     */
    public function store(Request $request)
    {
        $model = app($this->modelClass);
        
        // 게시판별 카테고리 가져오기
        $boardType = $this->getBoardTypeFromModelClass();
        $validCategories = $this->getCategories($boardType);

        // 유효성 검사
        $this->validatePostRequest($request, $validCategories);

        // 트랜잭션 시작
        DB::beginTransaction();
        
        try {
            $imagePaths = [];
            $originalNames = [];
            $thumbnailPaths = [];
            $thumbnailOriginalNames = [];

            // 썸네일 이미지 처리
            if ($request->hasFile('mq_thumbnail_image')) {
                $file = $request->file('mq_thumbnail_image');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $randomName = Str::random(32).'.'.$extension;
                $file->storeAs($this->uploadPath, $randomName, 'public');

                $thumbnailPaths[] = $randomName;
                $thumbnailOriginalNames[] = $originalName;
            }

            // 이미지 처리 (첨부파일)
            if ($request->hasFile('mq_image')) {
                foreach ($request->file('mq_image') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $randomName = Str::random(32).'.'.$extension;
                    $file->storeAs($this->uploadPath, $randomName, 'public');

                    $imagePaths[] = $randomName;
                    $originalNames[] = $originalName;
                }
            }

            // 게시글 생성
            $board = new $model();
            $board->mq_title = $request->mq_title;
            $board->mq_content = $request->mq_content;
            $board->mq_category = $request->mq_category;
            $board->mq_user_id = Auth::user()->mq_user_id;
            $board->mq_view_cnt = 0;
            $board->mq_like_cnt = 0;
            $board->mq_status = 1;
            
            if (!empty($imagePaths)) {
                $board->mq_image = $imagePaths;
                $board->mq_original_image = $originalNames;
            }

            if (!empty($thumbnailPaths)) {
                $board->mq_thumbnail_image = $thumbnailPaths;
                $board->mq_thumbnail_original = $thumbnailOriginalNames;
            }

            $board->mq_reg_date = now();
            $board->save();
            
            DB::commit();
            
            return redirect()->route($this->routePrefix.'.show', $board->idx)
                ->with('success', '게시글이 등록되었습니다.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => '저장 중 오류가 발생했습니다: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 게시글 상세 보기
     */
    public function show($idx)
    {
        $model = app($this->modelClass);
        $post = $model::where('mq_status', 1)->findOrFail($idx);
        
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
            $isLiked = DB::table('mq_like_history')
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
     * 게시글 수정 폼
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
     * 게시글 업데이트
     */
    public function update(Request $request, $idx)
    {
        $model = app($this->modelClass);
        $board = $model::findOrFail($idx);
        
        // 권한 검사
        if (!$this->canEdit($board)) {
            return redirect()->route($this->routePrefix.'.show', $idx)
                ->with('error', '수정 권한이 없습니다.');
        }

        // 게시판별 카테고리 가져오기
        $boardType = $this->getBoardTypeFromModelClass();
        $validCategories = $this->getCategories($boardType);

        // 유효성 검사
        $this->validatePostRequest($request, $validCategories);
        
        // 트랜잭션 시작
        DB::beginTransaction();
        
        try {
            $board->mq_title = $request->mq_title;
            $board->mq_content = $request->mq_content;
            $board->mq_category = $request->mq_category;
            
            // 썸네일 이미지 처리
            if ($request->hasFile('mq_thumbnail_image')) {
                $file = $request->file('mq_thumbnail_image');
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $randomName = Str::random(32).'.'.$extension;
                    $file->storeAs($this->uploadPath, $randomName, 'public');

                    $board->mq_thumbnail_image = [$randomName];
                    $board->mq_thumbnail_original = [$originalName];
                }
            }

            // 이미지 처리 (첨부파일)
            $imagePaths = is_array($board->mq_image) ? $board->mq_image : [];
            $originalNames = is_array($board->mq_original_image) ? $board->mq_original_image : [];

            if ($request->hasFile('mq_image')) {
                foreach ($request->file('mq_image') as $file) {
                    if ($file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $extension = $file->getClientOriginalExtension();
                        $randomName = Str::random(32).'.'.$extension;
                        $file->storeAs($this->uploadPath, $randomName, 'public');

                        $imagePaths[] = $randomName;
                        $originalNames[] = $originalName;
                    }
                }

                $board->mq_image = $imagePaths;
                $board->mq_original_image = $originalNames;
            }

            $board->mq_update_date = now();
            $board->save();
            
            DB::commit();
            
            return redirect()->route($this->routePrefix.'.show', $board->idx)
                ->with('success', '게시글이 수정되었습니다.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => '수정 중 오류가 발생했습니다: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 게시글 삭제
     */
    public function destroy($idx)
    {
        $model = app($this->modelClass);
        $board = $model::findOrFail($idx);
        
        // 권한 검사
        if (!$this->canDelete($board)) {
            return redirect()->route($this->routePrefix.'.show', $idx)
                ->with('error', '삭제 권한이 없습니다.');
        }
        
        // 트랜잭션 시작
        DB::beginTransaction();
        
        try {
            // 이미지 파일 삭제 (하드 삭제 시)
            if ($this->useHardDelete()) {
                // 첨부 이미지 삭제
                if (is_array($board->mq_image)) {
                    foreach ($board->mq_image as $image) {
                        Storage::disk('public')->delete($this->uploadPath . '/' . $image);
                    }
                }

                // 썸네일 이미지 삭제
                if (is_array($board->mq_thumbnail_image)) {
                    foreach ($board->mq_thumbnail_image as $thumbnail) {
                        Storage::disk('public')->delete($this->uploadPath . '/' . $thumbnail);
                    }
                }
            }

            // 삭제 처리 (소프트 or 하드)
            if ($this->useHardDelete()) {
                $board->delete();
            } else {
                $board->mq_status = 0;
                $board->save();
            }
            
            DB::commit();
            
            return redirect()->route($this->routePrefix.'.index')
                ->with('success', '게시글이 삭제되었습니다.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => '삭제 중 오류가 발생했습니다: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 좋아요 기능 (토글 방식: 좋아요 추가/취소)
     */
    public function like($idx)
    {
        try {
            $model = app($this->modelClass);
            $board = $model::findOrFail($idx);
            $user = Auth::user();

            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => '로그인이 필요한 기능입니다.'
                ], 401);
            }

            // 이미 좋아요를 눌렀는지 확인
            $existingLike = DB::table('mq_like_history')
                ->where('mq_user_id', $user->mq_user_id)
                ->where('mq_board_name', $this->getBoardTypeFromModelClass())
                ->where('mq_board_idx', $idx)
                ->first();

            if ($existingLike) {
                // 좋아요 취소: 기록 삭제 및 좋아요 수 감소
                DB::table('mq_like_history')
                    ->where('idx', $existingLike->idx)
                    ->delete();

                $board->decrement('mq_like_cnt');

                return response()->json([
                    'success' => true,
                    'likes' => $board->fresh()->mq_like_cnt,
                    'isLiked' => false,
                    'message' => '좋아요가 취소되었습니다.'
                ]);
            } else {
                // 좋아요 추가: 기록 추가 및 좋아요 수 증가
                DB::table('mq_like_history')->insert([
                    'mq_user_id' => $user->mq_user_id,
                    'mq_board_name' => $this->getBoardTypeFromModelClass(),
                    'mq_board_idx' => $idx,
                    'mq_reg_date' => now(),
                ]);

                $board->increment('mq_like_cnt');

                return response()->json([
                    'success' => true,
                    'likes' => $board->fresh()->mq_like_cnt,
                    'isLiked' => true,
                    'message' => '좋아요가 추가되었습니다.'
                ]);
            }
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
            
            // 에디터 이미지 저장 경로
            $editorPath = str_replace('uploads/', 'uploads/editor_', $this->uploadPath);
            
            // 파일 저장
            $path = $file->storeAs($editorPath, $randomName, 'public');
            
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
            $model = app($this->modelClass);
            $board = $model::findOrFail($idx);

            // 작성자 체크
            if (!$this->canEdit($board)) {
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
                    Storage::disk('public')->delete($this->uploadPath . '/' . $filename);

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

    /**
     * 썸네일 삭제 API
     */
    public function deleteThumbnail($idx)
    {
        try {
            // 인증 사용자 확인
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => '로그인이 필요합니다.'
                ], 401);
            }

            $model = app($this->modelClass);
            $board = $model::findOrFail($idx);

            // 작성자 체크 (추가 보안 강화)
            if (!$this->canEdit($board)) {
                return response()->json([
                    'success' => false,
                    'message' => '삭제 권한이 없습니다.'
                ], 403);
            }

            // 썸네일이 있는지 확인
            if (is_array($board->mq_thumbnail_image) && !empty($board->mq_thumbnail_image)) {
                $thumbnailFilename = $board->mq_thumbnail_image[0];

                // 실제 파일 삭제
                Storage::disk('public')->delete($this->uploadPath . '/' . $thumbnailFilename);

                // 데이터베이스에서 썸네일 정보 제거
                $board->mq_thumbnail_image = null;
                $board->mq_thumbnail_original = null;
                $board->save();

                return response()->json([
                    'success' => true,
                    'message' => '썸네일이 삭제되었습니다.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => '썸네일을 찾을 수 없습니다.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '썸네일 삭제 중 오류가 발생했습니다.'
            ], 500);
        }
    }
    
    // 추상 메서드 및 커스터마이징 가능한 메서드들 정의
    
    /**
     * 권한 - 게시글 수정 가능 여부
     */
    protected function canEdit($post)
    {
        // 기본 구현은 작성자만 수정 가능
        return $post->mq_user_id === Auth::user()->mq_user_id;
    }
    
    /**
     * 권한 - 게시글 삭제 가능 여부
     */
    protected function canDelete($post)
    {
        // 기본 구현은 작성자만 삭제 가능
        return $post->mq_user_id === Auth::user()->mq_user_id;
    }
    
    /**
     * 게시글 목록 이미지 처리
     */
    protected function processListImages(&$posts)
    {
        foreach ($posts as $post) {
            $post->mq_image = $this->getThumbnailUrl($post);
        }
    }

    /**
     * 게시글의 썸네일 URL 반환 (폴백 없음)
     */
    protected function getThumbnailUrl($post)
    {
        if (!is_array($post->mq_thumbnail_image) || empty($post->mq_thumbnail_image)) {
            return null;
        }

        $filename = $post->mq_thumbnail_image[0];

        return filter_var($filename, FILTER_VALIDATE_URL)
            ? $filename
            : asset('storage/' . $this->uploadPath . '/' . $filename);
    }
    
    /**
     * 게시글 상세 페이지 이미지 처리
     */
    protected function processPostImages(&$post)
    {
        if (is_array($post->mq_image)) {
            $post->mq_image = array_map(function($filename) {
                return asset('storage/' . $this->uploadPath . '/' . $filename);
            }, $post->mq_image);
        } else {
            // 본문에서 이미지 추출
            $firstImageSrc = extractFirstImageSrc($post->mq_content);
            if ($firstImageSrc) {
                // 본문에 이미지가 있으면 빈 배열로 설정
                $post->mq_image = [];
            } else {
                // 이미지가 없으면 기본 이미지 설정
                $post->mq_image = [asset('images/content/no_image.jpeg')];
            }
        }
    }
    
    /**
     * 유효성 검사 규칙
     */
    protected function validatePostRequest(Request $request, array $validCategories)
    {
        return $request->validate([
            'mq_title' => 'required|max:255',
            'mq_content' => 'required',
            'mq_category' => 'required|in:' . implode(',', $validCategories),
            'mq_image.*' => 'nullable|image|max:2048',
            'mq_thumbnail_image' => 'nullable|image|max:2048'
        ]);
    }
    
    /**
     * 페이지당 게시글 수
     */
    protected function getItemsPerPage()
    {
        return 9;
    }
    
    /**
     * 하드 삭제 사용 여부 (기본값: 소프트 삭제)
     */
    protected function useHardDelete()
    {
        return false;
    }
    
    /**
     * 카테고리 목록 가져오기 (Trait 사용)
     */
    protected function getCategories($boardType)
    {
        if ($boardType === 'board_content') {
            return $this->getBoardContentCategories();
        } else if ($boardType === 'board_research') {
            return $this->getBoardResearchCategories();
        }
        
        // 기본 동작 (이전 코드)
        $model = app($this->modelClass);
        return $model::select('mq_category')
            ->distinct()
            ->orderBy('mq_category')
            ->pluck('mq_category')
            ->toArray();
    }
    
    /**
     * 모델 클래스명으로부터 게시판 타입 결정
     */
    protected function getBoardTypeFromModelClass()
    {
        if (strpos($this->modelClass, 'BoardContent') !== false) {
            return 'board_content';
        } else if (strpos($this->modelClass, 'BoardResearch') !== false) {
            return 'board_research';
        } else if (strpos($this->modelClass, 'BoardVideo') !== false) {
            return 'board_video';
        } else if (strpos($this->modelClass, 'BoardPortfolio') !== false) {
            return 'board_portfolio';
        }
        
        // 기본값
        return null;
    }
} 