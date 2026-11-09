<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BoardApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\PushApiController;

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

// 인사이트 만화 게시판 API 라우트
Route::post('/board-cartoon', [BoardApiController::class, 'storeCartoon']);

// 뉴스 API 라우트
Route::prefix('news')->group(function () {
    Route::get('/rss', [NewsApiController::class, 'rssList']);
    Route::get('/check', [NewsApiController::class, 'checkDuplicate']);
    Route::post('/check-duplicate-batch', [NewsApiController::class, 'checkDuplicateBatch']);

    Route::post('/', [NewsApiController::class, 'store']);

    // 오늘의 뉴스 1면 API (외부 프로그램용)
    Route::post('/top', [NewsApiController::class, 'storeTopNews']);
});

// 앱 푸시 발송 API (외부 프로그램용, X-Push-Key 공유 키 필요)
// 실수로 반복 호출해도 알림이 쏟아지지 않도록 컨트롤러가 전체 발송 횟수를 따로 제한한다.
Route::post('/push/send', [PushApiController::class, 'send'])->middleware('push.key');
