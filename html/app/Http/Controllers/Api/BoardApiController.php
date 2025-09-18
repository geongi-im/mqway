<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardContent;
use App\Models\BoardResearch;
use App\Models\BoardPortfolio;
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
            // 요청 데이터 검증
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string|max:50',
                'writer' => 'required|string|max:50',
                'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // 게시판 타입에 따른 업로드 경로와 모델 설정
            $uploadPath = 'uploads/board';
            $model = Board::class;

            if ($type === 'content') {
                $uploadPath = 'uploads/board_content';
                $model = BoardContent::class;
            } elseif ($type === 'research') {
                $uploadPath = 'uploads/board_research';
                $model = BoardResearch::class;
            }

            // 이미지 처리
            $imagePaths = [];
            $originalImageNames = [];

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

            // 썸네일 이미지 처리
            $thumbnailPath = null;
            $thumbnailOriginalName = null;

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

            return response()->json([
                'success' => true,
                'message' => '게시글이 성공적으로 등록되었습니다.',
                'data' => $responseData
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
} 