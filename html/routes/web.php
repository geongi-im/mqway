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
use App\Http\Controllers\CashflowController;
use App\Http\Controllers\BoardContentController;
use App\Http\Controllers\BoardResearchController;
use App\Http\Controllers\BoardVideoController;
use App\Http\Controllers\BoardPortfolioController;
use App\Http\Controllers\EconomyTermGameController;
use App\Http\Controllers\FinancialQuizController;
use App\Http\Controllers\RetirementCalculatorController;

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

// Introduce 페이지 (커뮤니티 소개)
Route::get('/introduce', function () {
    return view('introduce');
})->name('introduce');

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
// Route::get('/news', [NewsController::class, 'index']);

// 뉴스 게시판
Route::prefix('board-news')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('board-news.index');
});

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

// 추천 콘텐츠 게시판 (비회원도 볼 수 있음, 글쓰기는 회원만)
Route::prefix('board-content')->group(function () {
    // 비회원도 접근 가능한 라우트
    Route::get('/', [BoardContentController::class, 'index'])->name('board-content.index');
    Route::get('/create', [BoardContentController::class, 'create'])->name('board-content.create')->middleware('auth');
    Route::post('/', [BoardContentController::class, 'store'])->name('board-content.store')->middleware('auth');
    Route::get('/{idx}/edit', [BoardContentController::class, 'edit'])->name('board-content.edit')->middleware('auth');
    Route::put('/{idx}', [BoardContentController::class, 'update'])->name('board-content.update')->middleware('auth');
    Route::delete('/{idx}', [BoardContentController::class, 'destroy'])->name('board-content.destroy')->middleware('auth');
    Route::post('/{idx}/like', [BoardContentController::class, 'like'])->name('board-content.like')->middleware('auth');
    Route::post('/upload-image', [BoardContentController::class, 'uploadImage'])->name('board-content.upload.image')->middleware('auth');
    Route::post('/delete-image/{idx}/{filename}', [BoardContentController::class, 'deleteImage'])->name('board-content.delete-image')->middleware('auth');
    Route::get('/{idx}', [BoardContentController::class, 'show'])->name('board-content.show');
});

// 투자 리서치 게시판 (회원 전용)
Route::prefix('board-research')->middleware('auth')->group(function () {
    // 모든 라우트에 auth 미들웨어 적용
    Route::get('/', [BoardResearchController::class, 'index'])->name('board-research.index');
    Route::get('/create', [BoardResearchController::class, 'create'])->name('board-research.create');
    Route::post('/', [BoardResearchController::class, 'store'])->name('board-research.store');
    Route::get('/{idx}/edit', [BoardResearchController::class, 'edit'])->name('board-research.edit');
    Route::put('/{idx}', [BoardResearchController::class, 'update'])->name('board-research.update');
    Route::delete('/{idx}', [BoardResearchController::class, 'destroy'])->name('board-research.destroy');
    Route::post('/{idx}/like', [BoardResearchController::class, 'like'])->name('board-research.like');
    Route::post('/upload-image', [BoardResearchController::class, 'uploadImage'])->name('board-research.upload.image');
    Route::post('/delete-image/{idx}/{filename}', [BoardResearchController::class, 'deleteImage'])->name('board-research.delete-image');
    Route::get('/{idx}', [BoardResearchController::class, 'show'])->name('board-research.show');
});

// 쉽게 보는 경제 비디오 게시판 (비회원도 볼 수 있음, 글쓰기는 회원만)
Route::prefix('board-video')->group(function () {
    // 비회원도 접근 가능한 라우트
    Route::get('/', [BoardVideoController::class, 'index'])->name('board-video.index');
    Route::get('/create', [BoardVideoController::class, 'create'])->name('board-video.create')->middleware('auth');
    Route::post('/', [BoardVideoController::class, 'store'])->name('board-video.store')->middleware('auth');
    Route::get('/{idx}/edit', [BoardVideoController::class, 'edit'])->name('board-video.edit')->middleware('auth');
    Route::put('/{idx}', [BoardVideoController::class, 'update'])->name('board-video.update')->middleware('auth');
    Route::delete('/{idx}', [BoardVideoController::class, 'destroy'])->name('board-video.destroy')->middleware('auth');
    Route::post('/{idx}/like', [BoardVideoController::class, 'like'])->name('board-video.like')->middleware('auth');
    Route::post('/upload-image', [BoardVideoController::class, 'uploadImage'])->name('board-video.upload.image')->middleware('auth');
    Route::post('/delete-image/{idx}/{filename}', [BoardVideoController::class, 'deleteImage'])->name('board-video.delete-image')->middleware('auth');
    Route::get('/{idx}', [BoardVideoController::class, 'show'])->name('board-video.show');
});

// 투자대가의 포트폴리오 게시판 (비회원도 볼 수 있음, 글쓰기는 회원만)
Route::prefix('board-portfolio')->group(function () {
    // 비회원도 접근 가능한 라우트
    Route::get('/', [BoardPortfolioController::class, 'index'])->name('board-portfolio.index');
    Route::get('/create', [BoardPortfolioController::class, 'create'])->name('board-portfolio.create')->middleware('auth');
    Route::post('/', [BoardPortfolioController::class, 'store'])->name('board-portfolio.store')->middleware('auth');
    Route::get('/{idx}/edit', [BoardPortfolioController::class, 'edit'])->name('board-portfolio.edit')->middleware('auth');
    Route::put('/{idx}', [BoardPortfolioController::class, 'update'])->name('board-portfolio.update')->middleware('auth');
    Route::delete('/{idx}', [BoardPortfolioController::class, 'destroy'])->name('board-portfolio.destroy')->middleware('auth');
    Route::post('/{idx}/like', [BoardPortfolioController::class, 'like'])->name('board-portfolio.like')->middleware('auth');
    Route::get('/{idx}', [BoardPortfolioController::class, 'show'])->name('board-portfolio.show');
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

// Cashflow 라우트
Route::prefix('cashflow')->group(function () {
    Route::get('/introduction', [CashflowController::class, 'introduction'])->name('cashflow.introduction');
    Route::get('/process', [CashflowController::class, 'process'])->name('cashflow.process');
    Route::get('/helper', [CashflowController::class, 'helper'])->name('cashflow.helper')->middleware('auth');
});

// Tools 라우트
Route::prefix('tools')->group(function () {
    Route::get('/economy-term-game', [EconomyTermGameController::class, 'index'])->name('tools.economy-term-game');
    Route::post('/economy-term-game/store', [EconomyTermGameController::class, 'storeGameResult'])->name('tools.economy-term-game.store')->middleware('auth');
    Route::get('/economy-term-game/ranking', [EconomyTermGameController::class, 'getRanking'])->name('tools.economy-term-game.ranking');
    Route::get('/financial-quiz', [FinancialQuizController::class, 'index'])->name('tools.financial-quiz');
    Route::get('/retirement-calculator', [RetirementCalculatorController::class, 'index'])->name('tools.retirement-calculator');
});