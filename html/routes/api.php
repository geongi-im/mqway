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

Route::post('/board', [BoardApiController::class, 'store']);

// 뉴스 API 라우트
Route::prefix('news')->group(function () {
    Route::get('/rss', [NewsApiController::class, 'rssList']);
    Route::get('/check', [NewsApiController::class, 'checkDuplicate']);
    Route::post('/', [NewsApiController::class, 'store']);
});
