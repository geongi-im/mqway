<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\PickController;
use App\Http\Controllers\BeecubeController;
use App\Http\Controllers\LifeSearchController;
use App\Http\Controllers\DataInsertController;
use App\Http\Controllers\RealityCheckController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GeminiBotController;
use App\Http\Controllers\MqtestController;
use App\Http\Controllers\Api\ServerCheckController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexController::class, 'index']);

// 로그인 페이지 (guest 미들웨어 적용)
Route::get('/login', function () {
    // 이미 로그인된 경우 메인 페이지로 리다이렉트
    if (Auth::check()) {
        return redirect('/');
    }
    return view('auth.login');
})->name('login')->middleware('guest');

// 로그아웃
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
});

// Google 로그인
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// 뉴스
Route::get('/news', [NewsController::class, 'index']);

// 게시판 (비회원 접근 가능)
Route::get('/board', [BoardController::class, 'index'])->name('board.index');
Route::get('/board/create', [BoardController::class, 'create'])->name('board.create');
Route::post('/board/upload-image', [BoardController::class, 'uploadImage'])->name('board.upload.image');
Route::post('/board', [BoardController::class, 'store'])->name('board.store');
Route::get('/board/{idx}', [BoardController::class, 'show'])->name('board.show');

// 게시판 (회원 전용)
Route::middleware('auth')->group(function () {
    Route::get('/board/{idx}/edit', [BoardController::class, 'edit'])->name('board.edit');
    Route::put('/board/{idx}', [BoardController::class, 'update'])->name('board.update');
    Route::delete('/board/{idx}', [BoardController::class, 'destroy'])->name('board.destroy');
    Route::post('/board/{idx}/like', [BoardController::class, 'like'])->name('board.like');
    Route::post('/board/delete-image/{idx}/{filename}', [BoardController::class, 'deleteImage'])->name('board.delete-image');
});

// Pick
Route::middleware('auth')->group(function () {
    Route::get('/pick', [PickController::class, 'index'])->name('pick.index');
    Route::post('/pick/start', [PickController::class, 'start'])->name('pick.start');
    Route::post('/pick/update', [PickController::class, 'update'])->name('pick.update');
    Route::post('/pick/prev', [PickController::class, 'prev'])->name('pick.prev');
});

// BeeCube
Route::middleware('auth')->group(function () {
    Route::get('/beecube', [BeecubeController::class, 'index'])->name('beecube.index');
    Route::post('/beecube/save', [BeecubeController::class, 'save'])->name('beecube.save');
});

// 원하는 삶 라우트
Route::prefix('guidebook/life-search')->group(function () {
    Route::get('/get-categories', [LifeSearchController::class, 'getCategories'])->name('guidebook.life-search.get-categories');
    Route::get('/get-samples', [LifeSearchController::class, 'getSamples'])->name('guidebook.life-search.get-samples');
    Route::post('/apply-samples', [LifeSearchController::class, 'applySamples'])->name('guidebook.life-search.apply-samples');
    Route::get('/', [LifeSearchController::class, 'index'])->name('guidebook.life-search');
    Route::post('/', [LifeSearchController::class, 'store'])->name('guidebook.life-search.store');
    Route::put('/{id}', [LifeSearchController::class, 'update'])->name('guidebook.life-search.update');
    Route::delete('/{id}', [LifeSearchController::class, 'destroy'])->name('guidebook.life-search.destroy');
});

// 현실 점검 라우트
Route::prefix('guidebook/reality-check')->group(function () {
    Route::get('/', [RealityCheckController::class, 'index'])->name('guidebook.reality-check');
    Route::post('/', [RealityCheckController::class, 'store'])->name('guidebook.reality-check.store');
    Route::put('/{idx}', [RealityCheckController::class, 'update'])->name('guidebook.reality-check.update');
    Route::delete('/{idx}', [RealityCheckController::class, 'destroy'])->name('guidebook.reality-check.destroy');
    Route::get('/samples', [RealityCheckController::class, 'getSamples'])->name('guidebook.reality-check.samples');
    Route::post('/apply-sample', [RealityCheckController::class, 'applySample'])->name('guidebook.reality-check.apply-sample');
    Route::get('/get-expenses', [RealityCheckController::class, 'getExpenses'])->name('guidebook.reality-check.get-expenses');
});

// 로드맵
Route::get('/guidebook/roadmap', [RoadmapController::class, 'index'])->name('guidebook.roadmap');

// 개인정보처리방침
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// 서비스 이용약관
Route::get('/service', function () {
    return view('service');
})->name('service');

// 챗봇 API 라우트
Route::post('/api/chatbot', [GeminiBotController::class, 'sendMessage'])->name('chatbot.send');
Route::post('/api/chatbot/reset', [GeminiBotController::class, 'resetConversation'])->name('chatbot.reset');

// 경제 상식 테스트
Route::get('/api/quiz', [MqtestController::class, 'getQuizData'])->name('quiz.get');

// 서버 체크 API
Route::get('/api/server-check', [ServerCheckController::class, 'serverCheck'])->name('api.server-check');
