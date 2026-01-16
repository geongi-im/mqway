<?php

namespace App\Http\Controllers;

use App\Models\BoardInsights;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\BoardCategoryColorTrait;

class BoardInsightsController extends AbstractBoardController
{
    use BoardCategoryColorTrait;
    
    protected $modelClass = BoardInsights::class;
    protected $viewPath = 'board_insights';
    protected $routePrefix = 'board-insights';
    protected $uploadPath = 'uploads/board_insights';

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    protected function getItemsPerPage()
    {
        return 15;
    }
    
    protected function useHardDelete()
    {
        return true;
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            
            $request->validate([
                'upload' => 'required|image|max:2048'
            ]);
            
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            $randomName = Str::random(32) . '.' . $extension;
            
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
    
    public function create()
    {
        $categories = $this->getBoardInsightsCategories();
        
        return view($this->viewPath.'.create', [
            'categories' => $categories
        ]);
    }
}
