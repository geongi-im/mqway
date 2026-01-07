<?php

namespace App\Http\Controllers;

use App\Models\BoardCartoon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Traits\BoardCategoryColorTrait;

class BoardCartoonController extends AbstractBoardController
{
    use BoardCategoryColorTrait;

    protected $modelClass = BoardCartoon::class;
    protected $viewPath = 'board_cartoon';
    protected $routePrefix = 'board-cartoon';
    protected $uploadPath = 'uploads/board_cartoon';

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    protected function getItemsPerPage()
    {
        return 12;
    }

    protected function useHardDelete()
    {
        return true;
    }

    /**
     * 게시글 저장 (자동 썸네일 설정)
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

            // 이미지 처리 (첨부파일) - 먼저 처리
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
            // 자동 썸네일 설정: 썸네일 없고 본문 이미지 있으면 첫 이미지를 썸네일로
            elseif (!empty($imagePaths)) {
                $thumbnailPaths[] = $imagePaths[0];
                $thumbnailOriginalNames[] = $originalNames[0];
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
     * 게시글 업데이트 (자동 썸네일 설정)
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
            // 자동 썸네일 설정: 썸네일 없고 본문 이미지 있으면 첫 이미지를 썸네일로
            elseif (empty($board->mq_thumbnail_image) && !empty($imagePaths)) {
                $board->mq_thumbnail_image = [$imagePaths[0]];
                $board->mq_thumbnail_original = [$originalNames[0]];
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
     * Validation 규칙 오버라이드 (mq_image를 required로)
     */
    protected function validatePostRequest(Request $request, array $validCategories)
    {
        return $request->validate([
            'mq_title' => 'required|max:255',
            'mq_content' => 'required',
            'mq_category' => 'required|in:' . implode(',', $validCategories),
            'mq_image.*' => 'required|image|max:2048',
            'mq_thumbnail_image' => 'nullable|image|max:2048'
        ]);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            $request->validate([
                'upload' => 'required|image|max:2048'
            ]);

            $extension = $file->getClientOriginalExtension();
            $randomName = Str::random(32) . '.' . $extension;

            $path = $file->storeAs($this->uploadPath . '/editor', $randomName, 'public');

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

    public function create()
    {
        $categories = $this->getBoardCartoonCategories();

        return view($this->viewPath . '.create', [
            'categories' => $categories
        ]);
    }
}
