<?php

namespace App\Http\Controllers;

use App\Models\BoardScrap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BoardScrapController extends Controller
{
    protected $uploadPath = 'uploads/board_scrap';

    /**
     * 생성자 - 목록/상세는 비회원도 열람 가능, 나머지는 회원 전용
     *
     * checkDuplicate 는 AJAX 로 호출되므로 미들웨어를 걸지 않고
     * 컨트롤러 안에서 JSON 401(requireLogin)을 직접 응답한다.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'checkDuplicate']);
    }

    /**
     * 뉴스 스크랩 목록
     *
     * 기본: 공개된 스크랩 전체 (비회원 열람 가능)
     * ?mine=1: 본인이 작성한 스크랩 전체 (공개 + 나만보기, 회원 전용)
     */
    public function index(Request $request)
    {
        $userId = Auth::check() ? Auth::user()->mq_user_id : null;
        $mine = $request->boolean('mine');

        // 내 스크랩 보기는 로그인 필요
        if ($mine && !$userId) {
            return redirect()->guest(route('login'));
        }

        $query = BoardScrap::with('user')->active();

        if ($mine) {
            $query->ownedBy($userId);

            // 공개 여부 필터 (내 스크랩에서만 의미 있음)
            if ($request->visibility === 'public') {
                $query->where('mq_is_public', 1);
            } elseif ($request->visibility === 'private') {
                $query->where('mq_is_public', 0);
            }
        } else {
            $query->publicOnly();
        }

        // 검색 처리
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('mq_title', 'like', '%'.$searchTerm.'%')
                  ->orWhere('mq_reason', 'like', '%'.$searchTerm.'%')
                  ->orWhere('mq_new_terms', 'like', '%'.$searchTerm.'%');
            });
        }

        // 정렬
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'views':
                $query->orderBy('mq_view_cnt', 'desc');
                break;
            case 'likes':
                $query->orderBy('mq_like_cnt', 'desc');
                break;
            default:
                // 공개 목록은 공개로 전환한 시점 기준 (오래된 글을 오늘 공개해도 상단에 노출)
                $query->orderBy($mine ? 'mq_reg_date' : 'mq_public_date', 'desc');
        }
        $query->orderBy('idx', 'desc'); // 동일 시각 글의 페이지네이션 순서 고정

        $scraps = $query->paginate(12);

        // 내 스크랩 탭의 공개/나만보기 개수
        $myCounts = null;
        if ($mine) {
            $myCounts = [
                'public' => BoardScrap::active()->ownedBy($userId)->where('mq_is_public', 1)->count(),
                'private' => BoardScrap::active()->ownedBy($userId)->where('mq_is_public', 0)->count(),
            ];
        }

        return view('board_scrap.index', [
            'scraps' => $scraps,
            'mine' => $mine,
            'sort' => $sort,
            'visibility' => $request->visibility,
            'myCounts' => $myCounts,
        ]);
    }

    /**
     * 글쓰기 폼
     */
    public function create()
    {
        return view('board_scrap.create');
    }

    /**
     * 저장
     */
    public function store(Request $request)
    {
        // 유효성 검사
        $request->validate([
            'mq_title' => 'required|string|max:500',
            'mq_url' => 'required|url|max:2000',
            'mq_reason' => 'required|string',
            'mq_new_terms' => 'nullable|string|max:5000',
            'mq_is_public' => 'nullable|boolean'
        ], [
            'mq_title.required' => '뉴스 제목을 입력해주세요.',
            'mq_title.max' => '뉴스 제목은 500자 이내로 입력해주세요.',
            'mq_url.required' => '뉴스 링크를 입력해주세요.',
            'mq_url.url' => '올바른 URL 형식이 아닙니다.',
            'mq_reason.required' => '뉴스를 선택한 이유를 입력해주세요.',
        ]);

        DB::beginTransaction();

        try {
            // URL에서 자동으로 썸네일 추출
            $thumbnailUrl = $this->extractThumbnailFromUrl($request->mq_url);

            // 체크하지 않으면 나만보기 (기본값)
            $isPublic = $request->boolean('mq_is_public');

            $scrap = new BoardScrap();
            $scrap->mq_user_id = Auth::user()->mq_user_id;
            $scrap->mq_title = $request->mq_title;
            $scrap->mq_url = $request->mq_url;
            $scrap->mq_reason = $request->mq_reason;
            $scrap->mq_new_terms = $request->mq_new_terms;
            $scrap->mq_thumbnail_url = $thumbnailUrl; // 자동 추출된 썸네일
            $scrap->mq_is_public = $isPublic ? 1 : 0;
            $scrap->mq_public_date = $isPublic ? Carbon::now() : null;
            $scrap->mq_status = 1;
            $scrap->mq_reg_date = Carbon::now();

            $scrap->save();

            DB::commit();

            return redirect()
                ->route('board-scrap.show', $scrap->idx)
                ->with('success', $isPublic
                    ? '뉴스 스크랩이 등록되어 공개 게시판에 공유되었습니다.'
                    : '뉴스 스크랩이 등록되었습니다. (나만보기)');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', '뉴스 스크랩 등록 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * 상세보기 (공개글은 누구나, 나만보기는 작성자 본인만)
     */
    public function show($idx)
    {
        $userId = Auth::check() ? Auth::user()->mq_user_id : null;

        $scrap = BoardScrap::with('user')
                        ->active()
                        ->visibleTo($userId)
                        ->where('idx', $idx)
                        ->firstOrFail();

        $isOwner = $userId !== null && $scrap->isOwner($userId);

        // 조회수 증가 (본인 조회 제외 + 세션으로 중복 증가 방지)
        if (!$isOwner && !session()->has('viewed_news_scrap_'.$idx)) {
            $scrap->increment('mq_view_cnt');
            session(['viewed_news_scrap_'.$idx => true]);
        }

        $isLiked = false;
        if ($userId) {
            $isLiked = DB::table('mq_like_history')
                ->where('mq_user_id', $userId)
                ->where('mq_board_name', BoardScrap::BOARD_NAME)
                ->where('mq_board_idx', $idx)
                ->exists();
        }

        return view('board_scrap.show', [
            'scrap' => $scrap,
            'isOwner' => $isOwner,
            'isLiked' => $isLiked
        ]);
    }

    /**
     * 수정 폼
     */
    public function edit($idx)
    {
        $scrap = BoardScrap::where('idx', $idx)
                        ->where('mq_user_id', Auth::user()->mq_user_id)
                        ->where('mq_status', 1)
                        ->firstOrFail();

        return view('board_scrap.edit', [
            'scrap' => $scrap
        ]);
    }

    /**
     * 수정
     */
    public function update(Request $request, $idx)
    {
        $scrap = BoardScrap::where('idx', $idx)
                        ->where('mq_user_id', Auth::user()->mq_user_id)
                        ->where('mq_status', 1)
                        ->firstOrFail();

        // 유효성 검사
        $request->validate([
            'mq_title' => 'required|string|max:500',
            'mq_url' => 'required|url|max:2000',
            'mq_reason' => 'required|string',
            'mq_new_terms' => 'nullable|string|max:5000',
            'mq_is_public' => 'nullable|boolean'
        ], [
            'mq_title.required' => '뉴스 제목을 입력해주세요.',
            'mq_title.max' => '뉴스 제목은 500자 이내로 입력해주세요.',
            'mq_url.required' => '뉴스 링크를 입력해주세요.',
            'mq_url.url' => '올바른 URL 형식이 아닙니다.',
            'mq_reason.required' => '뉴스를 선택한 이유를 입력해주세요.',
        ]);

        DB::beginTransaction();

        try {
            // URL이 변경된 경우에만 썸네일 재추출
            if ($scrap->mq_url !== $request->mq_url) {
                $thumbnailUrl = $this->extractThumbnailFromUrl($request->mq_url);
                $scrap->mq_thumbnail_url = $thumbnailUrl;
            }

            $wasPublic = $scrap->isPublic();
            $isPublic = $request->boolean('mq_is_public');

            $scrap->mq_title = $request->mq_title;
            $scrap->mq_url = $request->mq_url;
            $scrap->mq_reason = $request->mq_reason;
            $scrap->mq_new_terms = $request->mq_new_terms;
            $scrap->mq_is_public = $isPublic ? 1 : 0;
            $scrap->mq_update_date = Carbon::now();

            // 나만보기 -> 공개로 전환한 시점을 기록 (공개 목록 정렬 기준)
            if ($isPublic && !$wasPublic) {
                $scrap->mq_public_date = Carbon::now();
            }

            $scrap->save();

            DB::commit();

            return redirect()
                ->route('board-scrap.show', $scrap->idx)
                ->with('success', '뉴스 스크랩이 수정되었습니다.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', '뉴스 스크랩 수정 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * 삭제
     */
    public function destroy($idx)
    {
        $scrap = BoardScrap::where('idx', $idx)
                        ->where('mq_user_id', Auth::user()->mq_user_id)
                        ->where('mq_status', 1)
                        ->firstOrFail();

        DB::beginTransaction();

        try {
            // 소프트 삭제 (상태만 변경)
            $scrap->mq_status = 0;
            $scrap->mq_update_date = Carbon::now();
            $scrap->save();

            DB::commit();

            return redirect()
                ->route('board-scrap.index', ['mine' => 1])
                ->with('success', '뉴스 스크랩이 삭제되었습니다.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', '뉴스 스크랩 삭제 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * 공개 / 나만보기 전환 (작성자 본인만)
     *
     * @param int $idx
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleVisibility($idx)
    {
        $scrap = BoardScrap::active()
                        ->ownedBy(Auth::user()->mq_user_id)
                        ->where('idx', $idx)
                        ->first();

        if (!$scrap) {
            return response()->json([
                'success' => false,
                'message' => '스크랩을 찾을 수 없습니다.'
            ], 404);
        }

        try {
            $nextIsPublic = !$scrap->isPublic();

            $scrap->mq_is_public = $nextIsPublic ? 1 : 0;

            // 공개로 전환하는 시점을 기록 (공개 목록 정렬 기준)
            if ($nextIsPublic) {
                $scrap->mq_public_date = Carbon::now();
            }

            $scrap->save();

            return response()->json([
                'success' => true,
                'isPublic' => $nextIsPublic,
                'message' => $nextIsPublic
                    ? '공개 게시판에 공유되었습니다.'
                    : '나만보기로 변경되었습니다.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '공개 설정 변경 중 오류가 발생했습니다.'
            ], 500);
        }
    }

    /**
     * 좋아요 (토글). 공개된 스크랩만 대상.
     *
     * @param int $idx
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($idx)
    {
        $scrap = BoardScrap::active()
                        ->publicOnly()
                        ->where('idx', $idx)
                        ->first();

        if (!$scrap) {
            return response()->json([
                'success' => false,
                'message' => '공개된 스크랩만 좋아요를 누를 수 있습니다.'
            ], 404);
        }

        try {
            $userId = Auth::user()->mq_user_id;

            $existingLike = DB::table('mq_like_history')
                ->where('mq_user_id', $userId)
                ->where('mq_board_name', BoardScrap::BOARD_NAME)
                ->where('mq_board_idx', $idx)
                ->first();

            if ($existingLike) {
                // 좋아요 취소
                DB::table('mq_like_history')
                    ->where('idx', $existingLike->idx)
                    ->delete();

                if ($scrap->mq_like_cnt > 0) {
                    $scrap->decrement('mq_like_cnt');
                }

                return response()->json([
                    'success' => true,
                    'likes' => $scrap->fresh()->mq_like_cnt,
                    'isLiked' => false,
                    'message' => '좋아요가 취소되었습니다.'
                ]);
            }

            // 좋아요 추가
            DB::table('mq_like_history')->insert([
                'mq_user_id' => $userId,
                'mq_board_name' => BoardScrap::BOARD_NAME,
                'mq_board_idx' => $idx,
                'mq_reg_date' => Carbon::now(),
            ]);

            $scrap->increment('mq_like_cnt');

            return response()->json([
                'success' => true,
                'likes' => $scrap->fresh()->mq_like_cnt,
                'isLiked' => true,
                'message' => '좋아요가 추가되었습니다.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '좋아요 처리 중 오류가 발생했습니다.'
            ], 500);
        }
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
                'upload' => 'required|image|max:5120'
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
     * URL에서 Meta 이미지 추출 (PHP 내장 함수만 사용)
     */
    public function fetchMetaImage(Request $request)
    {
        $url = $request->input('url');

        // URL 유효성 검사
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'error' => '유효하지 않은 URL입니다.'
            ], 400);
        }

        try {
            // User-Agent 설정하여 HTML 가져오기
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
                ]
            ]);

            $html = @file_get_contents($url, false, $context);

            if ($html === false) {
                return response()->json([
                    'success' => false,
                    'error' => 'URL에서 데이터를 가져올 수 없습니다.'
                ], 400);
            }

            // DOMDocument로 파싱
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);

            $imageUrl = null;
            $title = null;

            // 1. Open Graph 이미지 찾기 (우선순위 1)
            $ogImage = $xpath->query("//meta[@property='og:image']/@content");
            if ($ogImage->length > 0) {
                $imageUrl = $ogImage->item(0)->nodeValue;
            }

            // 2. Twitter Card 이미지 찾기 (우선순위 2)
            if (!$imageUrl) {
                $twitterImage = $xpath->query("//meta[@name='twitter:image']/@content");
                if ($twitterImage->length > 0) {
                    $imageUrl = $twitterImage->item(0)->nodeValue;
                }
            }

            // 3. 일반 meta 이미지 찾기 (우선순위 3)
            if (!$imageUrl) {
                $metaImage = $xpath->query("//meta[@name='image']/@content");
                if ($metaImage->length > 0) {
                    $imageUrl = $metaImage->item(0)->nodeValue;
                }
            }

            // 4. link rel="image_src" 찾기 (우선순위 4)
            if (!$imageUrl) {
                $linkImage = $xpath->query("//link[@rel='image_src']/@href");
                if ($linkImage->length > 0) {
                    $imageUrl = $linkImage->item(0)->nodeValue;
                }
            }

            // 제목 추출 (Open Graph title 우선)
            $ogTitle = $xpath->query("//meta[@property='og:title']/@content");
            if ($ogTitle->length > 0) {
                $title = $ogTitle->item(0)->nodeValue;
            } else {
                // 일반 title 태그
                $titleTag = $xpath->query("//title");
                if ($titleTag->length > 0) {
                    $title = $titleTag->item(0)->nodeValue;
                }
            }

            // 상대 경로를 절대 경로로 변환
            if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($url);
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

                if (strpos($imageUrl, '/') === 0) {
                    // /로 시작하는 경우
                    $imageUrl = $baseUrl . $imageUrl;
                } else {
                    // 상대 경로인 경우
                    $imageUrl = $baseUrl . '/' . $imageUrl;
                }
            }

            if ($imageUrl) {
                return response()->json([
                    'success' => true,
                    'thumbnail_url' => $imageUrl,
                    'title' => $title
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => '이미지를 찾을 수 없습니다.'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => '이미지 추출 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * URL에서 썸네일 이미지를 추출하는 private 메서드
     *
     * @param string $url
     * @return string|null
     */
    private function extractThumbnailFromUrl($url)
    {
        try {
            // User-Agent 설정하여 HTML 가져오기
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n",
                    'timeout' => 10 // 10초 타임아웃
                ]
            ]);

            $html = @file_get_contents($url, false, $context);

            if ($html === false) {
                return null; // 실패 시 null 반환 (노이미지 표시)
            }

            // DOMDocument로 파싱
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);
            $imageUrl = null;

            // 1. Open Graph 이미지 찾기 (우선순위 1)
            $ogImage = $xpath->query("//meta[@property='og:image']/@content");
            if ($ogImage->length > 0) {
                $imageUrl = $ogImage->item(0)->nodeValue;
            }

            // 2. Twitter Card 이미지 찾기 (우선순위 2)
            if (!$imageUrl) {
                $twitterImage = $xpath->query("//meta[@name='twitter:image']/@content");
                if ($twitterImage->length > 0) {
                    $imageUrl = $twitterImage->item(0)->nodeValue;
                }
            }

            // 3. 일반 meta 이미지 찾기 (우선순위 3)
            if (!$imageUrl) {
                $metaImage = $xpath->query("//meta[@name='image']/@content");
                if ($metaImage->length > 0) {
                    $imageUrl = $metaImage->item(0)->nodeValue;
                }
            }

            // 4. link rel="image_src" 찾기 (우선순위 4)
            if (!$imageUrl) {
                $linkImage = $xpath->query("//link[@rel='image_src']/@href");
                if ($linkImage->length > 0) {
                    $imageUrl = $linkImage->item(0)->nodeValue;
                }
            }

            // 상대 경로를 절대 경로로 변환
            if ($imageUrl && !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($url);
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

                if (strpos($imageUrl, '/') === 0) {
                    // /로 시작하는 경우
                    $imageUrl = $baseUrl . $imageUrl;
                } else {
                    // 상대 경로인 경우
                    $imageUrl = $baseUrl . '/' . $imageUrl;
                }
            }

            return $imageUrl; // 찾지 못한 경우 null 반환

        } catch (\Exception $e) {
            // 오류 발생 시 null 반환 (노이미지 표시)
            return null;
        }
    }

    /**
     * 뉴스 URL 중복 체크 (스크랩 버튼용)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDuplicate(Request $request)
    {
        // 로그인 체크
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'exists' => false,
                'requireLogin' => true,
                'message' => '로그인이 필요합니다.'
            ], 401);
        }

        $url = $request->input('url');
        $userId = Auth::user()->mq_user_id;

        // URL 유효성 검사
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json([
                'success' => false,
                'exists' => false,
                'message' => '유효하지 않은 URL입니다.'
            ], 400);
        }

        // 중복 체크: 현재 사용자가 해당 URL을 이미 스크랩했는지 확인
        $exists = BoardScrap::where('mq_user_id', $userId)
                          ->where('mq_url', $url)
                          ->where('mq_status', 1)
                          ->exists();

        return response()->json([
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? '이미 스크랩된 뉴스입니다.' : '스크랩 가능합니다.'
        ]);
    }
}
