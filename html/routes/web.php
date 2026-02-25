<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FindInfoController;
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
use App\Http\Controllers\CourseController;
use App\Http\Controllers\BoardContentController;
use App\Http\Controllers\BoardCartoonController;
use App\Http\Controllers\BoardResearchController;
use App\Http\Controllers\BoardVideoController;
use App\Http\Controllers\BoardPortfolioController;
use App\Http\Controllers\BoardInsightsController;
use App\Http\Controllers\BoardMissionController;
use App\Http\Controllers\EconomyTermGameController;
use App\Http\Controllers\FinancialQuizController;
use App\Http\Controllers\RetirementCalculatorController;
use App\Http\Controllers\NeedWantGameController;
use App\Http\Controllers\Api\CashflowApiController;
use App\Http\Controllers\NewsScrapController;

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

// 일반 로그인 처리
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');

// 로그아웃
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
});

// 회원가입
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->middleware('guest');
Route::post('/register/check-userid', [App\Http\Controllers\Auth\RegisterController::class, 'checkUserId'])->name('register.check-userid')->middleware('guest');
Route::post('/register/check-email', [App\Http\Controllers\Auth\RegisterController::class, 'checkEmail'])->name('register.check-email')->middleware('guest');

// Google 로그인
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// Kakao 로그인
Route::get('/auth/kakao', [LoginController::class, 'redirectToKakao'])->name('login.kakao');
Route::get('/auth/kakao/callback', [LoginController::class, 'handleKakaoCallback']);

// Naver 로그인
Route::get('/auth/naver', [LoginController::class, 'redirectToNaver'])->name('login.naver');
Route::get('/auth/naver/callback', [LoginController::class, 'handleNaverCallback']);

// 회원정보 찾기
Route::get('/findinfo', [FindInfoController::class, 'showFindInfoForm'])->name('findinfo')->middleware('guest');
Route::post('/findinfo/find-id', [FindInfoController::class, 'findUserId'])->name('findinfo.find-id')->middleware('guest');
Route::post('/findinfo/reset-password', [FindInfoController::class, 'resetPassword'])->name('findinfo.reset-password')->middleware('guest');

// 뉴스
// Route::get('/news', [NewsController::class, 'index']);

// 뉴스 게시판
Route::prefix('board-news')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('board-news.index');
    Route::get('/top-news/{date}', [NewsController::class, 'getTopNewsByDate'])->name('board-news.top-news');
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
    Route::delete('/{idx}/thumbnail', [BoardContentController::class, 'deleteThumbnail'])->name('board-content.thumbnail.delete')->middleware('auth');
    Route::get('/{idx}', [BoardContentController::class, 'show'])->name('board-content.show');
});

// 인사이트 만화 게시판 (비회원도 볼 수 있음, 글쓰기는 회원만)
Route::prefix('board-cartoon')->group(function () {
    // 비회원도 접근 가능한 라우트
    Route::get('/', [BoardCartoonController::class, 'index'])->name('board-cartoon.index');
    Route::get('/create', [BoardCartoonController::class, 'create'])->name('board-cartoon.create')->middleware('auth');
    Route::post('/', [BoardCartoonController::class, 'store'])->name('board-cartoon.store')->middleware('auth');
    Route::get('/{idx}/edit', [BoardCartoonController::class, 'edit'])->name('board-cartoon.edit')->middleware('auth');
    Route::put('/{idx}', [BoardCartoonController::class, 'update'])->name('board-cartoon.update')->middleware('auth');
    Route::delete('/{idx}', [BoardCartoonController::class, 'destroy'])->name('board-cartoon.destroy')->middleware('auth');
    Route::post('/{idx}/like', [BoardCartoonController::class, 'like'])->name('board-cartoon.like')->middleware('auth');
    Route::post('/upload-image', [BoardCartoonController::class, 'uploadImage'])->name('board-cartoon.upload.image')->middleware('auth');
    Route::post('/delete-image/{idx}/{filename}', [BoardCartoonController::class, 'deleteImage'])->name('board-cartoon.delete-image')->middleware('auth');
    Route::delete('/{idx}/thumbnail', [BoardCartoonController::class, 'deleteThumbnail'])->name('board-cartoon.thumbnail.delete')->middleware('auth');
    Route::get('/{idx}', [BoardCartoonController::class, 'show'])->name('board-cartoon.show');
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
    Route::post('/upload-image', [BoardResearchController::class, 'uploadImage'])->name('board-research.upload.image')->middleware('auth');
    Route::post('/delete-image/{idx}/{filename}', [BoardResearchController::class, 'deleteImage'])->name('board-research.delete-image')->middleware('auth');
    Route::delete('/{idx}/thumbnail', [BoardResearchController::class, 'deleteThumbnail'])->name('board-research.thumbnail.delete')->middleware('auth');
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

// 투자 인사이트 게시판 (회원 전용)
Route::prefix('board-insights')->middleware('auth')->group(function () {
    Route::get('/', [BoardInsightsController::class, 'index'])->name('board-insights.index');
    Route::get('/create', [BoardInsightsController::class, 'create'])->name('board-insights.create');
    Route::post('/', [BoardInsightsController::class, 'store'])->name('board-insights.store');
    Route::post('/upload-image', [BoardInsightsController::class, 'uploadImage'])->name('board-insights.upload.image');
    Route::get('/{idx}/edit', [BoardInsightsController::class, 'edit'])->name('board-insights.edit');
    Route::put('/{idx}', [BoardInsightsController::class, 'update'])->name('board-insights.update');
    Route::delete('/{idx}', [BoardInsightsController::class, 'destroy'])->name('board-insights.destroy');
    Route::post('/{idx}/like', [BoardInsightsController::class, 'like'])->name('board-insights.like');
    Route::post('/delete-image/{idx}/{filename}', [BoardInsightsController::class, 'deleteImage'])->name('board-insights.delete-image');
    Route::delete('/{idx}/thumbnail', [BoardInsightsController::class, 'deleteThumbnail'])->name('board-insights.delete-thumbnail');
    Route::get('/{idx}', [BoardInsightsController::class, 'show'])->name('board-insights.show');
});

// 미션 게시판 (회원 전용, 본인 글만 열람 가능, 관리자는 전체)
Route::prefix('board-mission')->middleware('auth')->group(function () {
    Route::get('/', [BoardMissionController::class, 'index'])->name('board-mission.index');
    Route::get('/create', [BoardMissionController::class, 'create'])->name('board-mission.create');
    Route::post('/', [BoardMissionController::class, 'store'])->name('board-mission.store');
    Route::post('/upload-image', [BoardMissionController::class, 'uploadImage'])->name('board-mission.upload.image');
    Route::get('/{idx}/edit', [BoardMissionController::class, 'edit'])->name('board-mission.edit');
    Route::put('/{idx}', [BoardMissionController::class, 'update'])->name('board-mission.update');
    Route::delete('/{idx}', [BoardMissionController::class, 'destroy'])->name('board-mission.destroy');
    Route::post('/{idx}/like', [BoardMissionController::class, 'like'])->name('board-mission.like');
    Route::post('/delete-image/{idx}/{filename}', [BoardMissionController::class, 'deleteImage'])->name('board-mission.delete-image');
    Route::delete('/{idx}/thumbnail', [BoardMissionController::class, 'deleteThumbnail'])->name('board-mission.delete-thumbnail');
    Route::get('/{idx}', [BoardMissionController::class, 'show'])->name('board-mission.show');
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

// 마이페이지
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [App\Http\Controllers\MyPageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile', [App\Http\Controllers\MyPageController::class, 'profile'])->name('mypage.profile');
    Route::post('/mypage/profile', [App\Http\Controllers\MyPageController::class, 'updateProfile'])->name('mypage.profile.update');
    Route::delete('/mypage/profile/image', [App\Http\Controllers\MyPageController::class, 'deleteProfileImage'])->name('mypage.profile.image.delete');
    Route::post('/mypage/check-email', [App\Http\Controllers\MyPageController::class, 'checkEmail'])->name('mypage.check-email');
    Route::post('/mypage/check-current-password', [App\Http\Controllers\MyPageController::class, 'checkCurrentPassword'])->name('mypage.check-current-password');
    Route::post('/mypage/change-password', [App\Http\Controllers\MyPageController::class, 'changePassword'])->name('mypage.change-password');
    Route::get('/mypage/mapping', [App\Http\Controllers\MyPageController::class, 'mapping'])->name('mypage.mapping');
    Route::get('/mypage/mapping/items', [App\Http\Controllers\MyPageController::class, 'getMappingItems'])->name('mypage.mapping.items');
    Route::post('/mypage/mapping/save', [App\Http\Controllers\MyPageController::class, 'saveMapping'])->name('mypage.mapping.save');
    Route::get('/mypage/liked-content', [App\Http\Controllers\MyPageController::class, 'likedContent'])->name('mypage.liked-content');
    Route::post('/mypage/liked-content/unlike', [App\Http\Controllers\MyPageController::class, 'unlikeContent'])->name('mypage.liked-content.unlike');
});

// 뉴스 스크랩 (회원 전용)
Route::prefix('mypage/news-scrap')->middleware('auth')->group(function () {
    Route::get('/', [NewsScrapController::class, 'index'])->name('mypage.news-scrap.index');
    Route::get('/create', [NewsScrapController::class, 'create'])->name('mypage.news-scrap.create');
    Route::post('/', [NewsScrapController::class, 'store'])->name('mypage.news-scrap.store');
    Route::get('/{idx}', [NewsScrapController::class, 'show'])->name('mypage.news-scrap.show');
    Route::get('/{idx}/edit', [NewsScrapController::class, 'edit'])->name('mypage.news-scrap.edit');
    Route::put('/{idx}', [NewsScrapController::class, 'update'])->name('mypage.news-scrap.update');
    Route::delete('/{idx}', [NewsScrapController::class, 'destroy'])->name('mypage.news-scrap.destroy');
    Route::post('/upload-image', [NewsScrapController::class, 'uploadImage'])->name('mypage.news-scrap.upload-image');
    Route::post('/fetch-meta', [NewsScrapController::class, 'fetchMetaImage'])->name('mypage.news-scrap.fetch-meta');
    Route::post('/check-duplicate', [NewsScrapController::class, 'checkDuplicate'])->name('mypage.news-scrap.check-duplicate');
});

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
    Route::get('/intro', [CashflowController::class, 'intro'])->name('cashflow.intro');
    Route::get('/process', [CashflowController::class, 'process'])->name('cashflow.process');
    Route::get('/helper', [CashflowController::class, 'helper'])->name('cashflow.helper')->middleware('auth');
});

// 코스 소개 라우트
Route::prefix('course')->group(function () {
    Route::get('/l1/intro', [CourseController::class, 'l1Intro'])->name('course.l1.intro');
    Route::get('/l2/intro', [CourseController::class, 'l2Intro'])->name('course.l2.intro');

    // 코스 진행 상태 API
    Route::get('/progress/{courseCode}', [CourseController::class, 'getProgress'])->name('course.progress.get');
    Route::post('/progress/toggle', [CourseController::class, 'toggleProgress'])->name('course.progress.toggle');
});

// Tools 라우트
Route::prefix('tools')->group(function () {
    Route::get('/economy-term-game', [EconomyTermGameController::class, 'index'])->name('tools.economy-term-game');
    Route::post('/economy-term-game/store', [EconomyTermGameController::class, 'storeGameResult'])->name('tools.economy-term-game.store')->middleware('auth');
    Route::get('/economy-term-game/ranking', [EconomyTermGameController::class, 'getRanking'])->name('tools.economy-term-game.ranking');
    Route::get('/financial-quiz', [FinancialQuizController::class, 'index'])->name('tools.financial-quiz');
    Route::get('/retirement-calculator', [RetirementCalculatorController::class, 'index'])->name('tools.retirement-calculator');
    Route::get('/need-want-game', [NeedWantGameController::class, 'index'])->name('tools.need-want-game');
});

// 캐시플로우 게임 API 라우트 (web에서 세션 기반 인증 사용)
Route::prefix('api/cashflow')->middleware('auth')->group(function () {
    Route::post('/test', [CashflowApiController::class, 'test']);
    Route::post('/save', [CashflowApiController::class, 'saveGameState']);
    Route::post('/load', [CashflowApiController::class, 'loadGameState']);
    Route::post('/games', [CashflowApiController::class, 'getUserGames']);
    Route::delete('/game', [CashflowApiController::class, 'deleteGame']);
});