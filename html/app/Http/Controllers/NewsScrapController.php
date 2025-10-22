<?php

namespace App\Http\Controllers;

use App\Models\NewsScrap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewsScrapController extends Controller
{
    protected $uploadPath = 'uploads/news_scrap';

    /**
     * 생성자 - 회원 인증 미들웨어 적용
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 뉴스 스크랩 목록 (본인 것만 조회)
     */
    public function index(Request $request)
    {
        $userId = Auth::user()->mq_user_id;

        $query = NewsScrap::where('mq_user_id', $userId)
                         ->where('mq_status', 1);

        // 검색 처리
        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('mq_title', 'like', '%'.$searchTerm.'%')
                  ->orWhere('mq_reason', 'like', '%'.$searchTerm.'%')
                  ->orWhere('mq_new_terms', 'like', '%'.$searchTerm.'%');
            });
        }

        // 정렬 (최신순)
        $query->orderBy('mq_reg_date', 'desc');

        $scraps = $query->paginate(12);

        return view('mypage.news_scrap.index', [
            'scraps' => $scraps
        ]);
    }

    /**
     * 글쓰기 폼
     */
    public function create()
    {
        return view('mypage.news_scrap.create');
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
            'mq_new_terms' => 'nullable|string|max:5000'
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

            $scrap = new NewsScrap();
            $scrap->mq_user_id = Auth::user()->mq_user_id;
            $scrap->mq_title = $request->mq_title;
            $scrap->mq_url = $request->mq_url;
            $scrap->mq_reason = $request->mq_reason;
            $scrap->mq_new_terms = $request->mq_new_terms;
            $scrap->mq_thumbnail_url = $thumbnailUrl; // 자동 추출된 썸네일
            $scrap->mq_status = 1;
            $scrap->mq_reg_date = Carbon::now();

            $scrap->save();

            DB::commit();

            return redirect()
                ->route('mypage.news-scrap.index')
                ->with('success', '뉴스 스크랩이 등록되었습니다.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', '뉴스 스크랩 등록 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }

    /**
     * 상세보기
     */
    public function show($idx)
    {
        $scrap = NewsScrap::where('idx', $idx)
                        ->where('mq_user_id', Auth::user()->mq_user_id)
                        ->where('mq_status', 1)
                        ->firstOrFail();

        return view('mypage.news_scrap.show', [
            'scrap' => $scrap
        ]);
    }

    /**
     * 수정 폼
     */
    public function edit($idx)
    {
        $scrap = NewsScrap::where('idx', $idx)
                        ->where('mq_user_id', Auth::user()->mq_user_id)
                        ->where('mq_status', 1)
                        ->firstOrFail();

        return view('mypage.news_scrap.edit', [
            'scrap' => $scrap
        ]);
    }

    /**
     * 수정
     */
    public function update(Request $request, $idx)
    {
        $scrap = NewsScrap::where('idx', $idx)
                        ->where('mq_user_id', Auth::user()->mq_user_id)
                        ->where('mq_status', 1)
                        ->firstOrFail();

        // 유효성 검사
        $request->validate([
            'mq_title' => 'required|string|max:500',
            'mq_url' => 'required|url|max:2000',
            'mq_reason' => 'required|string',
            'mq_new_terms' => 'nullable|string|max:5000'
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

            $scrap->mq_title = $request->mq_title;
            $scrap->mq_url = $request->mq_url;
            $scrap->mq_reason = $request->mq_reason;
            $scrap->mq_new_terms = $request->mq_new_terms;
            $scrap->mq_update_date = Carbon::now();

            $scrap->save();

            DB::commit();

            return redirect()
                ->route('mypage.news-scrap.show', $scrap->idx)
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
        $scrap = NewsScrap::where('idx', $idx)
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
                ->route('mypage.news-scrap.index')
                ->with('success', '뉴스 스크랩이 삭제되었습니다.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', '뉴스 스크랩 삭제 중 오류가 발생했습니다: ' . $e->getMessage());
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
        $exists = NewsScrap::where('mq_user_id', $userId)
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
