<?php

namespace App\Http\Controllers;

use App\Models\BoardVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\BoardCategoryColorTrait;
use Illuminate\Support\Facades\DB;

class BoardVideoController extends AbstractBoardController
{
    use BoardCategoryColorTrait;
    
    protected $modelClass = BoardVideo::class;
    protected $viewPath = 'board_video';
    protected $routePrefix = 'board-video';
    protected $uploadPath = 'uploads/board_video';
    
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
        return 12;
    }
    
    /**
     * 하드 삭제 사용 여부 설정
     */
    protected function useHardDelete()
    {
        return true;
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
            
            // uploadPath 디렉토리에 저장
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
    
    /**
     * 게시글 작성 폼 표시 - 고정된 카테고리 사용
     */
    public function create()
    {
        // 고정된 카테고리 목록 사용
        $categories = $this->getBoardVideoCategories();
        
        // 필요한 경우 추가 데이터를 뷰에 전달
        return view($this->viewPath.'.create', [
            'categories' => $categories
        ]);
    }
    
    /**
     * 게시글 저장 - 비디오 URL 처리 추가
     */
    public function store(Request $request)
    {
        $model = app($this->modelClass);
        
        // 게시판별 카테고리 가져오기
        $boardType = $this->getBoardTypeFromModelClass();
        $validCategories = $this->getCategories($boardType);

        // 유효성 검사 - 비디오 URL 추가
        $this->validatePostRequest($request, $validCategories);
        
        // 비디오 URL 유효성 검증
        $request->validate([
            'mq_video_url' => 'required|url'
        ]);

        // 트랜잭션 시작
        DB::beginTransaction();
        
        try {
            $imagePaths = [];
            $originalNames = [];

            // 이미지 처리
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
            $board->mq_video_url = $request->mq_video_url; // 비디오 URL 저장
            
            if (!empty($imagePaths)) {
                $board->mq_image = $imagePaths;
                $board->mq_original_image = $originalNames;
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
     * 게시글 업데이트 - 비디오 URL 처리 추가
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

        // 유효성 검사 - 비디오 URL 추가
        $this->validatePostRequest($request, $validCategories);
        
        // 비디오 URL 유효성 검증
        $request->validate([
            'mq_video_url' => 'required|url'
        ]);
        
        // 트랜잭션 시작
        DB::beginTransaction();
        
        try {
            $board->mq_title = $request->mq_title;
            $board->mq_content = $request->mq_content;
            $board->mq_category = $request->mq_category;
            $board->mq_video_url = $request->mq_video_url; // 비디오 URL 업데이트
            
            // 이미지 처리
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
     * 게시글 목록 이미지 처리 - YouTube 썸네일 추출 추가
     */
    protected function processListImages(&$posts)
    {
        foreach ($posts as $post) {
            if (is_array($post->mq_image) && !empty($post->mq_image)) {
                // 업로드된 이미지가 있으면 그걸 사용
                $filename = $post->mq_image[0];
                $post->mq_image = !filter_var($filename, FILTER_VALIDATE_URL) 
                    ? asset('storage/' . $this->uploadPath . '/' . $filename)
                    : $filename;
            } else if (isset($post->mq_video_url) && !empty($post->mq_video_url)) {
                // 비디오 URL이 있으면 YouTube 썸네일 추출
                $thumbnailUrl = $this->getVideoThumbnail($post->mq_video_url);
                if ($thumbnailUrl) {
                    $post->mq_image = $thumbnailUrl;
                } else {
                    // 본문에서 이미지 추출
                    $firstImageSrc = extractFirstImageSrc($post->mq_content);
                    if ($firstImageSrc) {
                        $post->mq_image = $firstImageSrc;
                    } else {
                        // 이미지가 없으면 기본 이미지 설정
                        $post->mq_image = asset('images/content/no_image.jpeg');
                    }
                }
            } else {
                // 본문에서 이미지 추출
                $firstImageSrc = extractFirstImageSrc($post->mq_content);
                if ($firstImageSrc) {
                    $post->mq_image = $firstImageSrc;
                } else {
                    // 이미지가 없으면 기본 이미지 설정
                    $post->mq_image = asset('images/content/no_image.jpeg');
                }
            }
        }
    }
    
    /**
     * 게시글 상세 페이지 이미지 처리 - YouTube 썸네일 추출 추가
     */
    protected function processPostImages(&$post)
    {
        if (is_array($post->mq_image) && !empty($post->mq_image)) {
            // 업로드된 이미지가 있으면 그걸 사용
            $post->mq_image = array_map(function($filename) {
                return asset('storage/' . $this->uploadPath . '/' . $filename);
            }, $post->mq_image);
        } else if (isset($post->mq_video_url) && !empty($post->mq_video_url)) {
            // 비디오 URL이 있지만 업로드 이미지가 없는 경우 비어있는 상태로 유지 (비디오가 표시됨)
            $post->mq_image = [];
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
     * 비디오 URL에서 썸네일 추출
     */
    protected function getVideoThumbnail($videoUrl)
    {
        // YouTube URL 패턴 인식
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $videoUrl, $matches) || 
            preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $videoUrl, $matches)) {
            $videoId = $matches[1];
            // YouTube 고품질 썸네일 URL 반환
            return "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
        }
        
        // Vimeo URL 패턴 인식 (Vimeo는 API 호출 필요로 복잡하므로 생략)
        // 다른 비디오 플랫폼은 필요에 따라 추가
        
        return null;
    }
} 