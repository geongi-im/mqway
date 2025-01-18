<?php

namespace App\Http\Controllers;

use App\Models\Beecube;
use Illuminate\Http\Request;

class BeecubeController extends Controller
{
    public function index()
    {
        $beecube = null;
        if (auth()->check() && false) {
            $beecube = Beecube::where('user_id', auth()->id())
                             ->latest()
                             ->first();
        }
        
        return view('beecube.index', compact('beecube'));
    }

    public function save(Request $request)
    {
        try {
            $beecube = Beecube::updateOrCreate(
                ['user_id' => auth()->id(), 'status' => 0],
                ['content' => $request->content]
            );

            return response()->json([
                'success' => true,
                'message' => '저장되었습니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '저장에 실패했습니다.'
            ]);
        }
    }
} 