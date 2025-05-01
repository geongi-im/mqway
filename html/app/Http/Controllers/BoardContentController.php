<?php

namespace App\Http\Controllers;

use App\Models\BoardContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\BoardCategoryColorTrait;

class BoardContentController extends AbstractBoardController
{
    use BoardCategoryColorTrait;
    
    protected $modelClass = BoardContent::class;
    protected $viewPath = 'board_content';
    protected $routePrefix = 'board-content';
    protected $uploadPath = 'uploads/board_content';
    
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
        $categories = $this->getBoardContentCategories();
        
        // 필요한 경우 추가 데이터를 뷰에 전달
        return view($this->viewPath.'.create', [
            'categories' => $categories
        ]);
    }
} 