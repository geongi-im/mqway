<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BoardApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            // 요청 데이터 검증
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'category' => 'required|string|max:50',
                'writer' => 'required|string|max:50',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // 이미지 처리
            $imagePath = null;
            $originalImageName = null;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $originalImageName = $image->getClientOriginalName();
                $randomName = Str::random(32) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('uploads/board', $randomName, 'public');
                $imagePath = Storage::url($imagePath);
            }

            // 게시글 생성
            $board = Board::create([
                'mq_title' => $request->title,
                'mq_content' => $request->content,
                'mq_category' => $request->category,
                'mq_writer' => $request->writer,
                'mq_image' => $imagePath,
                'mq_original_image' => $originalImageName,
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
                    'category' => $board->mq_category,
                    'writer' => $board->mq_writer,
                    'image_url' => $board->mq_image,
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