<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use App\Models\BoardContent;
use App\Models\BoardResearch;
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
                'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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

            // 게시글 생성
            $board = $model::create([
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
            ]);

            // 이미지 URL 생성
            $imageUrls = array_map(function($filename) use ($uploadPath) {
                return asset('storage/' . $uploadPath . '/' . $filename);
            }, $imagePaths);

            return response()->json([
                'success' => true,
                'message' => '게시글이 성공적으로 등록되었습니다.',
                'data' => [
                    'id' => $board->idx,
                    'title' => $board->mq_title,
                    'category' => $board->mq_category,
                    'writer' => $board->mq_user_id,
                    'image_urls' => $imageUrls,
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
} 