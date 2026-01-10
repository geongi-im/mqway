<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardContent;
use App\Models\BoardResearch;
use App\Models\BoardPortfolio;
use App\Models\BoardCartoon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BoardApiController extends Controller
{
    /**
     * 일반 게시판에 게시글 저장
     */
    public function store(Request $request)
    {
        return $this->processStore($request, 'general');
    }

    /**
     * 콘텐츠 게시판에 게시글 저장
     */
    public function storeContent(Request $request)
    {
        return $this->processStore($request, 'content');
    }

    /**
     * 리서치 게시판에 게시글 저장
     */
    public function storeResearch(Request $request)
    {
        return $this->processStore($request, 'research');
    }

    /**
     * 인사이트 만화 게시판에 게시글 저장
     */
    public function storeCartoon(Request $request)
    {
        return $this->processStore($request, 'cartoon');
    }

    /**
     * 포트폴리오 게시판에 게시글 저장
     */
    public function storePortfolio(Request $request)
    {
        try {
            // 요청 데이터 검증
            $request->validate([
                'title' => 'required|string|max:255',
                'portfolio_idx' => 'required|string',
                'investor_code' => 'required|string|max:50',
                'writer' => 'required|string|max:50'
            ]);

            $model = BoardPortfolio::class;
            $board = $model::create([
                'mq_title' => $request->title,
                'mq_portfolio_idx' => $request->portfolio_idx,
                'mq_investor_code' => $request->investor_code,
                'mq_user_id' => $request->writer,
                'mq_view_cnt' => 0,
                'mq_like_cnt' => 0,
                'mq_status' => 1,
                'mq_reg_date' => Carbon::now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => '게시글이 성공적으로 등록되었습니다.',
                'data' => [
                    'id' => $board->idx,
                    'title' => $board->mq_title,
                    'portfolio_idx' => $board->mq_portfolio_idx,
                    'investor_code' => $board->mq_investor_code,
                    'writer' => $board->mq_user_id,
                    'created_at' => $board->mq_reg_date->format('Y-m-d H:i:s')
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '입력값 검증 실패',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '게시글 등록 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 게시판 타입별 저장 프로세스 처리
     */
    private function processStore(Request $request, $type)
    {
        try {
            // upload_type 기본값 설정
            $uploadType = $request->input('upload_type', 'file');

            // upload_type에 따른 파라미터 충돌 검증
            if ($uploadType === 'url') {
                if ($request->hasFile('image') || $request->hasFile('thumbnail_image')) {
                    throw new \Exception('upload_type이 url일 때는 파일 업로드를 사용할 수 없습니다.');
                }
            } else {
                if ($request->has('image_urls') || $request->has('thumbnail_image_url')) {
                    throw new \Exception('upload_type이 file일 때는 URL 파라미터를 사용할 수 없습니다.');
                }
            }

            // 요청 데이터 검증
            $validationRules = [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string|max:50',
                'writer' => 'required|string|max:50',
                'upload_type' => 'nullable|in:file,url',
            ];

            // upload_type에 따라 다른 validation 적용
            if ($uploadType === 'url') {
                $validationRules['image_urls'] = 'nullable|array|max:10';
                $validationRules['image_urls.*'] = 'required|url|max:2048';
                $validationRules['thumbnail_image_url'] = 'nullable|url|max:2048';

                if ($type === 'cartoon') {
                    $validationRules['image_urls'] = 'required|array|min:1|max:10';
                }
            } else {
                // 기존 파일 업로드 규칙
                $validationRules['thumbnail_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';  // 5MB

                if ($type === 'cartoon') {
                    $validationRules['image'] = 'required|array|min:1';
                    $validationRules['image.*'] = 'required|image|mimes:jpeg,png,jpg,gif|max:5120';
                } else {
                    $validationRules['image.*'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120';
                }
            }

            $request->validate($validationRules);

            // 게시판 타입에 따른 업로드 경로와 모델 설정
            $uploadPath = 'uploads/board';
            $model = Board::class;

            if ($type === 'content') {
                $uploadPath = 'uploads/board_content';
                $model = BoardContent::class;
            } elseif ($type === 'research') {
                $uploadPath = 'uploads/board_research';
                $model = BoardResearch::class;
            } elseif ($type === 'cartoon') {
                $uploadPath = 'uploads/board_cartoon';
                $model = BoardCartoon::class;
            }

            // 이미지 처리
            $imagePaths = [];
            $originalImageNames = [];
            $tempFiles = []; // 임시 파일 경로 저장 (나중에 삭제)

            if ($uploadType === 'url') {
                // URL 기반 이미지 다운로드
                if ($request->has('image_urls') && is_array($request->image_urls)) {
                    foreach ($request->image_urls as $imageUrl) {
                        if (empty($imageUrl)) {
                            continue;
                        }

                        $result = $this->downloadImageFromUrl($imageUrl, $uploadPath);

                        if ($result['success']) {
                            $imagePaths[] = $result['filename'];
                            $originalImageNames[] = $result['original_name'];

                            // 임시 파일 경로 저장
                            if (isset($result['temp_path'])) {
                                $tempFiles[] = $result['temp_path'];
                            }
                        } else {
                            \Log::warning('Image URL download failed', [
                                'url' => $imageUrl,
                                'error' => $result['error'],
                                'board_type' => $type
                            ]);

                            // 만화 게시판에서 필수 이미지가 없으면 예외 발생
                            if ($type === 'cartoon' && empty($imagePaths)) {
                                throw new \Exception('이미지 다운로드 실패: ' . $result['error']);
                            }
                        }
                    }
                }
            } else {
                // 기존 파일 업로드 코드
                if ($request->hasFile('image')) {
                    foreach ($request->file('image') as $image) {
                        $originalImageName = $image->getClientOriginalName();
                        $extension = $image->getClientOriginalExtension();
                        $randomName = Str::random(32) . '.' . $extension;

                        // 파일 저장
                        $image->storeAs($uploadPath, $randomName, 'public');

                        // DB에는 파일명만 저장
                        $imagePaths[] = $randomName;
                        $originalImageNames[] = $originalImageName;
                    }
                }
            }

            // 만화 게시판 검증
            if ($type === 'cartoon' && empty($imagePaths)) {
                // 임시 파일 정리
                foreach ($tempFiles as $tempFile) {
                    if (file_exists($tempFile)) {
                        @unlink($tempFile);
                    }
                }
                throw new \Exception('만화 게시판은 최소 1개의 이미지가 필요합니다.');
            }

            // 썸네일 이미지 처리
            $thumbnailPath = null;
            $thumbnailOriginalName = null;

            if ($uploadType === 'url') {
                // URL 기반 썸네일 다운로드
                if ($request->has('thumbnail_image_url') && !empty($request->thumbnail_image_url)) {
                    $result = $this->downloadImageFromUrl(
                        $request->thumbnail_image_url,
                        $uploadPath
                    );

                    if ($result['success']) {
                        $thumbnailPath = $result['filename'];
                        $thumbnailOriginalName = $result['original_name'];

                        // 임시 파일 경로 저장
                        if (isset($result['temp_path'])) {
                            $tempFiles[] = $result['temp_path'];
                        }
                    } else {
                        \Log::warning('Thumbnail URL download failed', [
                            'url' => $request->thumbnail_image_url,
                            'error' => $result['error'],
                            'board_type' => $type
                        ]);
                    }
                }
                // 만화 게시판: 첫 번째 이미지를 썸네일로
                elseif ($type === 'cartoon' && !empty($imagePaths)) {
                    $thumbnailPath = $imagePaths[0];
                    $thumbnailOriginalName = $originalImageNames[0];
                }
            } else {
                // 기존 파일 업로드 코드
                if ($request->hasFile('thumbnail_image')) {
                    $thumbnailImage = $request->file('thumbnail_image');
                    $thumbnailOriginalName = $thumbnailImage->getClientOriginalName();
                    $extension = $thumbnailImage->getClientOriginalExtension();
                    $randomName = Str::random(32) . '.' . $extension;

                    // 파일 저장
                    $thumbnailImage->storeAs($uploadPath, $randomName, 'public');

                    // DB에는 파일명만 저장
                    $thumbnailPath = $randomName;
                }
                elseif ($type === 'cartoon' && !empty($imagePaths)) {
                    $thumbnailPath = $imagePaths[0];
                    $thumbnailOriginalName = $originalImageNames[0];
                }
            }

            // 게시글 생성 데이터 준비
            $boardData = [
                'mq_title' => $request->title,
                'mq_content' => $request->content,
                'mq_category' => $request->category,
                'mq_user_id' => $request->writer,
                'mq_image' => $imagePaths,
                'mq_original_image' => $originalImageNames,
                'mq_view_cnt' => 0,
                'mq_like_cnt' => 0,
                'mq_status' => 1,
                'mq_reg_date' => Carbon::now(),
            ];

            // 썸네일 이미지가 있는 경우만 추가
            if ($thumbnailPath) {
                $boardData['mq_thumbnail_image'] = [$thumbnailPath];
                $boardData['mq_thumbnail_original'] = [$thumbnailOriginalName];
            }

            // 게시글 생성
            $board = $model::create($boardData);

            // 이미지 URL 생성
            $imageUrls = array_map(function($filename) use ($uploadPath) {
                return asset('storage/' . $uploadPath . '/' . $filename);
            }, $imagePaths);

            // 썸네일 URL 생성
            $thumbnailUrl = null;
            if ($thumbnailPath) {
                $thumbnailUrl = asset('storage/' . $uploadPath . '/' . $thumbnailPath);
            }

            $responseData = [
                'id' => $board->idx,
                'title' => $board->mq_title,
                'category' => $board->mq_category,
                'writer' => $board->mq_user_id,
                'image_urls' => $imageUrls,
                'created_at' => $board->mq_reg_date->format('Y-m-d H:i:s')
            ];

            // 썸네일이 있는 경우만 추가
            if ($thumbnailUrl) {
                $responseData['thumbnail_url'] = $thumbnailUrl;
                $responseData['thumbnail_original_name'] = $thumbnailOriginalName;
            }

            // 임시 파일 정리
            foreach ($tempFiles as $tempFile) {
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }

            return response()->json([
                'success' => true,
                'message' => '게시글이 성공적으로 등록되었습니다.',
                'data' => $responseData
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // 임시 파일 정리
            if (isset($tempFiles)) {
                foreach ($tempFiles as $tempFile) {
                    if (file_exists($tempFile)) {
                        @unlink($tempFile);
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => '입력값 검증 실패',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // 임시 파일 정리
            if (isset($tempFiles)) {
                foreach ($tempFiles as $tempFile) {
                    if (file_exists($tempFile)) {
                        @unlink($tempFile);
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => '게시글 등록 중 오류가 발생했습니다.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * URL에서 이미지를 다운로드하여 임시 저장 후 최종 경로에 저장
     *
     * @param string $url 다운로드할 이미지 URL
     * @param string $uploadPath 저장 경로 (예: uploads/board_content)
     * @return array ['success' => bool, 'filename' => string, 'original_name' => string, 'temp_path' => string|null, 'error' => string]
     */
    private function downloadImageFromUrl(string $url, string $uploadPath): array
    {
        // 상수 정의
        $maxSize = 5 * 1024 * 1024; // 5MB
        $maxRetries = 3;
        $timeout = 30; // seconds

        // 허용된 MIME 타입
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $mimeToExtension = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        // URL 검증 (HTTP/HTTPS만 허용)
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], ['http', 'https'])) {
            return ['success' => false, 'error' => 'Only HTTP/HTTPS URLs are allowed'];
        }

        // 원본 파일명 추출
        $originalName = basename($parsedUrl['path'] ?? '');
        if (!$originalName || strpos($originalName, '.') === false) {
            $originalName = 'downloaded_image.jpg';
        }

        // HTTP 다운로드 (재시도 로직 - cURL 사용)
        $imageData = null;
        $lastError = '';
        $contentType = '';
        $contentLength = null;
        $statusCode = 0;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            curl_setopt($ch, CURLOPT_HEADER, false);

            $imageData = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $contentLength = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

            $error = curl_error($ch);
            curl_close($ch);

            if ($statusCode >= 200 && $statusCode < 300 && $imageData !== false) {
                break;
            }

            $lastError = $error ?: 'HTTP ' . $statusCode;

            if ($attempt === $maxRetries) {
                return [
                    'success' => false,
                    'error' => 'Download failed after ' . $maxRetries . ' retries: ' . $lastError
                ];
            }

            // 재시도 전 잠시 대기
            sleep(1);
        }

        // 응답 검증
        if ($imageData === false || empty($imageData)) {
            return [
                'success' => false,
                'error' => 'Download failed: ' . $lastError
            ];
        }

        // MIME 타입 검증
        $mimeValid = false;
        $detectedMime = null;

        foreach ($allowedMimes as $mime) {
            if (strpos($contentType, $mime) !== false) {
                $mimeValid = true;
                $detectedMime = $mime;
                break;
            }
        }

        if (!$mimeValid) {
            return [
                'success' => false,
                'error' => 'Invalid image type: ' . $contentType
            ];
        }

        // 파일 크기 검증
        if ($contentLength && $contentLength > $maxSize) {
            return [
                'success' => false,
                'error' => 'File size exceeds 5MB (Content-Length: ' . $contentLength . ')'
            ];
        }

        if (strlen($imageData) > $maxSize) {
            return [
                'success' => false,
                'error' => 'File size exceeds 5MB (Actual size: ' . strlen($imageData) . ')'
            ];
        }

        // 확장자 결정
        $extension = null;

        // 1. MIME 타입에서 확장자 추출
        if ($detectedMime && isset($mimeToExtension[$detectedMime])) {
            $extension = $mimeToExtension[$detectedMime];
        }

        // 2. URL 경로에서 확장자 추출
        if (!$extension) {
            $urlExtension = strtolower(pathinfo($parsedUrl['path'] ?? '', PATHINFO_EXTENSION));
            if (in_array($urlExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $extension = $urlExtension;
            }
        }

        // 3. 기본값
        if (!$extension) {
            $extension = 'jpg';
        }

        // 임시 파일로 저장
        $tempPath = sys_get_temp_dir() . '/' . uniqid('mqway_') . '_' . basename($originalName);

        try {
            file_put_contents($tempPath, $imageData);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to save temp file: ' . $e->getMessage()
            ];
        }

        // 파일명 생성 (기존 패턴과 동일)
        $randomName = Str::random(32) . '.' . $extension;

        // 최종 Storage에 저장
        try {
            Storage::disk('public')->put($uploadPath . '/' . $randomName, $imageData);

            // 저장 확인
            if (!Storage::disk('public')->exists($uploadPath . '/' . $randomName)) {
                @unlink($tempPath);
                return [
                    'success' => false,
                    'error' => 'Failed to verify saved file'
                ];
            }
        } catch (\Exception $e) {
            @unlink($tempPath);
            return [
                'success' => false,
                'error' => 'Failed to save file: ' . $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'filename' => $randomName,
            'original_name' => $originalName,
            'temp_path' => $tempPath
        ];
    }
}