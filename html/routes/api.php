<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BoardApiController;
use App\Http\Controllers\Api\NewsApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 일반 게시판 API 라우트
Route::post('/board', [BoardApiController::class, 'store']);

// 콘텐츠 게시판 API 라우트
Route::post('/board-content', [BoardApiController::class, 'storeContent']);

// 리서치 게시판 API 라우트
Route::post('/board-research', [BoardApiController::class, 'storeResearch']);

// 포트폴리오 게시판 API 라우트
Route::post('/board-portfolio', [BoardApiController::class, 'storePortfolio']);

// 뉴스 API 라우트
Route::prefix('news')->group(function () {
    Route::get('/rss', [NewsApiController::class, 'rssList']);
    Route::get('/check', [NewsApiController::class, 'checkDuplicate']);
    Route::post('/', [NewsApiController::class, 'store']);
});

// 캐시플로우 게임 API 라우트는 web.php로 이동됨 (세션 기반 인증 사용)
