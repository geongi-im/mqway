<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Traits\BoardCategoryColorTrait;

class MyPageController extends Controller
{
    use BoardCategoryColorTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // 좋아요한 콘텐츠 (기존 데이터 활용)
        $likedContent = [];
        if ($user) {
            $likedContent = DB::table('mq_like_history')
                ->where('mq_user_id', $user->mq_user_id)
                ->orderBy('mq_reg_date', 'desc')
                ->get()
                ->groupBy('mq_board_name');
        }

        return view('mypage.index', compact('user', 'likedContent'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'mq_user_name' => 'required|string|max:255',
            'mq_user_email' => [
                'required',
                'email',
                'max:255',
                'unique:mq_member,mq_user_email,' . $user->idx . ',idx'
            ],
            'mq_profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mq_birthday' => 'nullable|date',
        ], [
            'mq_user_email.unique' => '이미 사용 중인 이메일입니다.',
            'mq_user_email.required' => '이메일을 입력해주세요.',
            'mq_user_email.email' => '올바른 이메일 형식이 아닙니다.',
            'mq_user_name.required' => '이름을 입력해주세요.',
        ]);

        $updateData = [
            'mq_user_name' => $request->mq_user_name,
            'mq_user_email' => $request->mq_user_email,
            'mq_birthday' => $request->mq_birthday,
        ];

        // 프로필 이미지 처리
        if ($request->hasFile('mq_profile_image')) {
            // 기존 이미지 삭제
            if ($user->mq_profile_image && Storage::disk('public')->exists('uploads/profile/' . $user->mq_profile_image)) {
                Storage::disk('public')->delete('uploads/profile/' . $user->mq_profile_image);
            }

            // 새 이미지 업로드
            $image = $request->file('mq_profile_image');
            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $filename = 'profile_' . $user->mq_user_id . '_' . time() . '.' . $extension;

            // storage/app/public/uploads/profile에 저장
            $image->storeAs('uploads/profile', $filename, 'public');
            
            // DB에는 파일명만 저장
            $updateData['mq_profile_image'] = $filename;
        }

        $user->update($updateData);

        return redirect()->route('mypage.index')->with('success', '프로필이 성공적으로 업데이트되었습니다.');
    }

    public function deleteProfileImage(Request $request)
    {
        $user = Auth::user();

        if ($user->mq_profile_image && Storage::disk('public')->exists('uploads/profile/' . $user->mq_profile_image)) {
            Storage::disk('public')->delete('uploads/profile/' . $user->mq_profile_image);
        }

        $user->update(['mq_profile_image' => null]);

        return redirect()->route('mypage.index')->with('success', '프로필 이미지가 삭제되었습니다.');
    }

    public function mapping()
    {
        $user = Auth::user();

        // 생년월일 체크 - 없으면 프로필 페이지로 리다이렉트
        if (empty($user->mq_birthday)) {
            return redirect()->route('mypage.index')
                ->with('error', 'MQ 맵핑을 진행하려면 생일 정보가 필요합니다. 프로필에서 생일을 입력해주세요.');
        }

        // 전체 매핑 아이템 데이터
        $allMappingItems = $this->getAllMappingItems();

        // 초기 로드는 12개만
        $mappingItems = array_slice($allMappingItems, 0, 12);

        // 카테고리 목록
        $categories = [
            'all' => '전체',
            'creation' => '창작',
            'adventure' => '탐험',
            'challenge' => '도전',
            'growth' => '성장',
            'experience' => '경험',
            'custom' => '기타'
        ];

        // 사용자가 선택한 매핑 데이터 조회
        $userMappings = DB::table('mq_mapping_user as mu')
            ->join('mq_mapping_item as mi', 'mu.mi_idx', '=', 'mi.idx')
            ->where('mu.mq_user_id', $user->mq_user_id)
            ->select('mi.idx', 'mi.mq_category', 'mi.mq_image', 'mi.mq_description', 'mu.mq_target_year')
            ->get();

        // 선택된 아이템 정보를 배열로 변환
        $selectedItems = $userMappings->map(function($item) {
            $imagePath = $item->mq_image;
            if ($imagePath && !str_starts_with($imagePath, 'http')) {
                $imagePath = asset('storage/uploads/mapping/' . $imagePath);
            }

            return [
                'id' => $item->idx,
                'category' => $item->mq_category,
                'description' => $item->mq_description,
                'targetYear' => $item->mq_target_year,
                'imageSrc' => $imagePath
            ];
        })->keyBy('id')->toArray();

        return view('mypage.mapping', compact('user', 'mappingItems', 'categories', 'selectedItems'));
    }

    public function getMappingItems(Request $request)
    {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 6);
        $category = $request->get('category', 'all');

        // 전체 매핑 아이템
        $allItems = $this->getAllMappingItems();

        // 카테고리 필터링
        if ($category !== 'all') {
            $allItems = array_filter($allItems, function($item) use ($category) {
                return $item['category'] === $category;
            });
            $allItems = array_values($allItems); // 배열 인덱스 재정렬
        }

        // 페이징 처리
        $items = array_slice($allItems, $offset, $limit);
        $hasMore = count($allItems) > ($offset + $limit);

        // 딜레이 시뮬레이션 (실제 환경에서는 제거 가능)
        usleep(300000); // 0.3초

        return response()->json([
            'success' => true,
            'items' => $items,
            'hasMore' => $hasMore,
            'total' => count($allItems)
        ]);
    }

    private function getAllMappingItems()
    {
        $user = Auth::user();

        // 기본 5개 카테고리 (creation, adventure, challenge, growth, experience) 조회
        $basicItems = DB::table('mq_mapping_item')
            ->where('mq_status', 1)
            ->whereIn('mq_category', ['creation', 'adventure', 'challenge', 'growth', 'experience'])
            ->orderBy('idx', 'asc')
            ->get();

        // custom 카테고리는 현재 로그인한 사용자가 등록한 것만 조회
        $customItems = DB::table('mq_mapping_item as mi')
            ->join('mq_mapping_user as mu', 'mi.idx', '=', 'mu.mi_idx')
            ->where('mi.mq_status', 1)
            ->where('mi.mq_category', 'custom')
            ->where('mu.mq_user_id', $user->mq_user_id)
            ->select('mi.*')
            ->orderBy('mi.idx', 'asc')
            ->get();

        // 두 결과를 합침
        $items = $basicItems->merge($customItems);

        return $items->map(function($item) {
            // 이미지 경로 처리
            $imagePath = $item->mq_image;
            if ($imagePath && !str_starts_with($imagePath, 'http')) {
                // 로컬 이미지인 경우 storage 경로 추가
                $imagePath = asset('storage/uploads/mapping/' . $imagePath);
            }

            return [
                'id' => $item->idx,
                'category' => $item->mq_category,
                'image' => $imagePath,
                'description' => $item->mq_description
            ];
        })->toArray();
    }

    public function saveMapping(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
        }

        $itemId = $request->input('id');
        $description = $request->input('description');
        $targetYear = $request->input('targetYear');
        $action = $request->input('action');

        try {
            if ($action === 'add') {
                $miIdx = $itemId;
                $imageUrl = null;

                // 커스텀 목표인 경우 mq_mapping_item에 먼저 저장
                if (str_starts_with($itemId, 'custom-')) {
                    $imagePath = null;

                    // AI 생성 이미지가 있는 경우
                    $aiGeneratedFilename = $request->input('aiGeneratedFilename');
                    if ($aiGeneratedFilename && Storage::disk('public')->exists('uploads/mapping/' . $aiGeneratedFilename)) {
                        $imagePath = $aiGeneratedFilename;
                        $imageUrl = asset('storage/uploads/mapping/' . $aiGeneratedFilename);
                    } elseif ($request->hasFile('imageFile')) {
                        $image = $request->file('imageFile');
                        $extension = $image->getClientOriginalExtension() ?: 'png';
                        $filename = 'mapping_custom_' . $user->mq_user_id . '_' . time() . '.' . $extension;
                        $image->storeAs('uploads/mapping', $filename, 'public');
                        $imagePath = $filename;
                        $imageUrl = asset('storage/uploads/mapping/' . $filename);
                    }

                    // mq_mapping_item에 저장
                    $miIdx = DB::table('mq_mapping_item')->insertGetId([
                        'mq_category' => 'custom',
                        'mq_image' => $imagePath,
                        'mq_description' => $description,
                        'mq_status' => 1,
                        'mq_reg_date' => now(),
                        'mq_update_date' => now()
                    ]);
                }

                // mq_mapping_user에 저장
                DB::table('mq_mapping_user')->insert([
                    'mq_user_id' => $user->mq_user_id,
                    'mi_idx' => $miIdx,
                    'mq_target_year' => $targetYear,
                    'mq_reg_date' => now(),
                    'mq_update_date' => now()
                ]);

                return response()->json(['success' => true, 'mi_idx' => $miIdx, 'image_url' => $imageUrl]);

            } elseif ($action === 'update') {
                // 목표 연도 업데이트
                DB::table('mq_mapping_user')
                    ->where('mq_user_id', $user->mq_user_id)
                    ->where('mi_idx', $itemId)
                    ->update([
                        'mq_target_year' => $targetYear,
                        'mq_update_date' => now()
                    ]);

                // 커스텀 목표인 경우 설명도 업데이트
                $mappingItem = DB::table('mq_mapping_item')
                    ->where('idx', $itemId)
                    ->first();

                if ($mappingItem && $mappingItem->mq_category === 'custom') {
                    DB::table('mq_mapping_item')
                        ->where('idx', $itemId)
                        ->update([
                            'mq_description' => $description,
                            'mq_update_date' => now()
                        ]);
                }

                return response()->json(['success' => true]);

            } elseif ($action === 'remove') {
                // 매핑 정보 조회
                $mapping = DB::table('mq_mapping_user')
                    ->where('mq_user_id', $user->mq_user_id)
                    ->where('mi_idx', $itemId)
                    ->first();

                if ($mapping) {
                    // 커스텀 목표인지 확인
                    $mappingItem = DB::table('mq_mapping_item')
                        ->where('idx', $mapping->mi_idx)
                        ->first();

                    // mq_mapping_user에서 삭제
                    DB::table('mq_mapping_user')
                        ->where('mq_user_id', $user->mq_user_id)
                        ->where('mi_idx', $itemId)
                        ->delete();

                    // 커스텀 목표인 경우 mq_mapping_item과 이미지도 삭제
                    if ($mappingItem && $mappingItem->mq_category === 'custom') {
                        // 이미지 파일 삭제
                        if ($mappingItem->mq_image && Storage::disk('public')->exists('uploads/mapping/' . $mappingItem->mq_image)) {
                            Storage::disk('public')->delete('uploads/mapping/' . $mappingItem->mq_image);
                        }

                        // mq_mapping_item에서 삭제
                        DB::table('mq_mapping_item')
                            ->where('idx', $mapping->mi_idx)
                            ->delete();
                    }
                }

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => '알 수 없는 액션입니다.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * AI 이미지 생성 (Gemini 3.1 Flash Image Preview)
     * 목표 설명을 기반으로 Pixar 스타일 3D 이미지를 생성합니다.
     */
    public function generateMappingImage(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
        }

        $description = $request->input('description');
        if (empty($description)) {
            return response()->json(['success' => false, 'message' => '목표 설명을 입력해주세요.'], 400);
        }

        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'API 키가 설정되지 않았습니다.'], 500);
        }

        // 프롬프트 생성 (mq_mapping_image_generation_guide.md 기반)
        $prompt = $this->buildImagePrompt($description);

        try {
            $modelId = 'gemini-3.1-flash-image-preview';
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelId}:streamGenerateContent?key={$apiKey}";

            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseModalities' => ['IMAGE'],
                    'thinkingConfig' => [
                        'thinkingLevel' => 'MINIMAL',
                    ],
                    'imageConfig' => [
                        'imageSize' => '512',
                    ],
                ]
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 120,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return response()->json(['success' => false, 'message' => 'API 요청 실패: ' . $curlError], 500);
            }

            if ($httpCode !== 200) {
                return response()->json(['success' => false, 'message' => 'API 응답 오류 (HTTP ' . $httpCode . '): ' . $response], 500);
            }

            $data = json_decode($response, true);

            // streamGenerateContent 응답은 JSON 배열 형태로 반환됨
            // 배열의 각 요소에서 이미지 데이터를 찾음
            $imageData = null;
            $mimeType = 'image/png';

            // 응답이 배열인 경우 (streamGenerateContent)
            $chunks = is_array($data) && isset($data[0]) ? $data : [$data];

            foreach ($chunks as $chunk) {
                if (isset($chunk['candidates'][0]['content']['parts'])) {
                    foreach ($chunk['candidates'][0]['content']['parts'] as $part) {
                        if (isset($part['inlineData'])) {
                            $imageData = $part['inlineData']['data'];
                            $mimeType = $part['inlineData']['mimeType'] ?? 'image/png';
                            break 2;
                        }
                    }
                }
            }

            if (!$imageData) {
                return response()->json(['success' => false, 'message' => '이미지 생성에 실패했습니다. 다시 시도해주세요.'], 500);
            }

            // Base64 디코딩 후 파일 저장
            $imageBytes = base64_decode($imageData);
            $extension = str_contains($mimeType, 'png') ? 'png' : 'jpg';
            $filename = 'mapping_ai_' . $user->mq_user_id . '_' . time() . '_' . Str::random(4) . '.' . $extension;

            Storage::disk('public')->put('uploads/mapping/' . $filename, $imageBytes);

            $imageUrl = asset('storage/uploads/mapping/' . $filename);

            return response()->json([
                'success' => true,
                'image_url' => $imageUrl,
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => '이미지 생성 중 오류: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 목표 설명을 기반으로 Pixar 3D 스타일 이미지 프롬프트를 생성합니다.
     */
    private function buildImagePrompt($description)
    {
        return "A Pixar style 3D rendered scene showing a child (randomly boy or girl) achieving or working towards the goal: \"{$description}\".
The scene should visually represent this goal in a clear, child-friendly way with expressive character face and engaging environment.
Art style: Pixar animation movie quality with smooth rendering, rounded shapes, and vibrant colors.
Composition: Medium shot, well-balanced composition optimized for a square thumbnail.
Color palette: Bright, warm, and inviting colors appropriate for the scene.
Lighting: Warm, cheerful lighting that creates an optimistic atmosphere.
Background: Simple, relevant environment that supports the goal theme without being too complex.
Mood: Hopeful, determined, and inspiring.
Important: Absolutely NO text, NO Korean characters, NO letters, NO numbers, NO brand names visible anywhere in the image.
Square format optimized for thumbnail icon (512x512px).";
    }

    public function visionBoard()
    {
        $user = Auth::user();

        // 사용자가 선택한 매핑 데이터 조회
        $userMappings = DB::table('mq_mapping_user as mu')
            ->join('mq_mapping_item as mi', 'mu.mi_idx', '=', 'mi.idx')
            ->where('mu.mq_user_id', $user->mq_user_id)
            ->select('mi.idx', 'mi.mq_category', 'mi.mq_image', 'mi.mq_description', 'mu.mq_target_year')
            ->orderBy('mu.mq_target_year', 'asc')
            ->get();

        // 선택된 아이템 정보를 배열로 변환
        $selectedItems = $userMappings->map(function($item) {
            $imagePath = $item->mq_image;
            if ($imagePath && !str_starts_with($imagePath, 'http')) {
                $imagePath = asset('storage/uploads/mapping/' . $imagePath);
            }

            return [
                'id' => $item->idx,
                'category' => $item->mq_category,
                'description' => $item->mq_description,
                'targetYear' => $item->mq_target_year,
                'imageSrc' => $imagePath
            ];
        })->values()->toArray();

        // 카테고리 라벨
        $categoryLabels = [
            'creation' => '창작',
            'adventure' => '탐험',
            'challenge' => '도전',
            'growth' => '성장',
            'experience' => '경험',
            'custom' => '기타'
        ];

        // 저장된 비전보드 캔버스 데이터 조회
        $savedBoard = DB::table('mq_mapping_vision_board')
            ->where('mq_user_id', $user->mq_user_id)
            ->first();
        $canvasData = $savedBoard ? $savedBoard->canvas_data : null;

        return view('mypage.vision-board', compact('user', 'selectedItems', 'categoryLabels', 'canvasData'));
    }

    /**
     * 비전보드 캔버스 상태 저장 (AJAX)
     */
    public function saveVisionBoard(Request $request)
    {
        $user = Auth::user();
        $canvasData = $request->input('canvas_data');

        if (!$canvasData) {
            return response()->json(['success' => false, 'message' => '캔버스 데이터가 없습니다.'], 400);
        }

        $exists = DB::table('mq_mapping_vision_board')
            ->where('mq_user_id', $user->mq_user_id)
            ->exists();

        if ($exists) {
            DB::table('mq_mapping_vision_board')
                ->where('mq_user_id', $user->mq_user_id)
                ->update([
                    'canvas_data' => $canvasData,
                    'mq_update_date' => now()
                ]);
        } else {
            DB::table('mq_mapping_vision_board')->insert([
                'mq_user_id' => $user->mq_user_id,
                'canvas_data' => $canvasData,
                'mq_reg_date' => now()
            ]);
        }

        return response()->json(['success' => true]);
    }


    public function likedContent()
    {
        $user = Auth::user();

        // 게시판명 매핑 (mq_like_history에 저장된 값 기준)
        $boardLabels = [
            'board_content' => '추천 콘텐츠',
            'board_research' => '투자 리서치',
            'board_portfolio' => '투자대가의 포트폴리오',
            'board_video' => '경제 비디오',
            'board_cartoon' => '인사이트 만화',
            'board_insights' => '투자 인사이트',
        ];

        // 좋아요한 콘텐츠 가져오기
        $likedContent = collect();
        if ($user) {
            $likedItems = DB::table('mq_like_history')
                ->where('mq_user_id', $user->mq_user_id)
                ->orderBy('mq_reg_date', 'desc')
                ->get();

            // 게시판별로 그룹화
            $likedContent = $likedItems->groupBy('mq_board_name');

            // 각 게시물의 상세 정보 조회
            foreach ($likedContent as $boardName => $items) {
                foreach ($items as $key => $item) {
                    $post = null;
                    $boardUrl = '#';

                    // 게시판별로 실제 게시물 조회 (mq_like_history의 board_name은 mq_ 접두사 없음)
                    switch ($boardName) {
                        case 'board_content':
                            $post = DB::table('mq_board_content')
                                ->where('idx', $item->mq_board_idx)
                                ->first();
                            $boardUrl = route('board-content.show', $item->mq_board_idx);
                            break;
                        case 'board_research':
                            $post = DB::table('mq_board_research')
                                ->where('idx', $item->mq_board_idx)
                                ->first();
                            $boardUrl = route('board-research.show', $item->mq_board_idx);
                            break;
                        case 'board_portfolio':
                            $post = DB::table('mq_board_portfolio')
                                ->where('idx', $item->mq_board_idx)
                                ->first();
                            $boardUrl = route('board-portfolio.show', $item->mq_board_idx);
                            break;
                        case 'board_video':
                            $post = DB::table('mq_board_video')
                                ->where('idx', $item->mq_board_idx)
                                ->first();
                            $boardUrl = route('board-video.show', $item->mq_board_idx);
                            break;
                        case 'board_cartoon':
                            $post = DB::table('mq_board_cartoon')
                                ->where('idx', $item->mq_board_idx)
                                ->first();
                            $boardUrl = route('board-cartoon.show', $item->mq_board_idx);
                            break;
                        case 'board_insights':
                            $post = DB::table('mq_board_insights')
                                ->where('idx', $item->mq_board_idx)
                                ->first();
                            $boardUrl = route('board-insights.show', $item->mq_board_idx);
                            break;
                    }

                    // 게시물 정보 추가
                    $item->post = $post;
                    $item->boardUrl = $boardUrl;
                }
            }
        }

        // 카테고리별 색상 매핑
        $categoryColors = $this->getCategoryColors();

        return view('mypage.liked-content', compact('user', 'likedContent', 'boardLabels', 'categoryColors'));
    }

    public function unlikeContent(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
        }

        $boardName = $request->input('board_name');
        $boardIdx = $request->input('board_idx');

        // 좋아요 기록 삭제
        $deleted = DB::table('mq_like_history')
            ->where('mq_user_id', $user->mq_user_id)
            ->where('mq_board_name', $boardName)
            ->where('mq_board_idx', $boardIdx)
            ->delete();

        if ($deleted) {
            // mq_like_history에 저장된 mq_board_name 값은 'board_content', 'board_research' 등
            // mq_ 접두사가 없으므로, 실제 테이블명을 변환해야 함
            $tableName = $this->convertBoardNameToTableName($boardName);

            // 해당 게시판의 좋아요 수 감소
            DB::table($tableName)
                ->where('idx', $boardIdx)
                ->decrement('mq_like_cnt');

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => '좋아요 취소에 실패했습니다.']);
    }

    /**
     * mq_like_history의 board_name 값을 실제 테이블명으로 변환
     */
    private function convertBoardNameToTableName($boardName)
    {
        $mapping = [
            'board_content' => 'mq_board_content',
            'board_research' => 'mq_board_research',
            'board_portfolio' => 'mq_board_portfolio',
            'board_video' => 'mq_board_video',
            'board_cartoon' => 'mq_board_cartoon',
        ];

        return $mapping[$boardName] ?? $boardName;
    }

    /**
     * 이메일 중복 체크 (마이페이지용)
     * 현재 사용자의 이메일은 중복으로 간주하지 않음
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request)
    {
        $user = Auth::user();
        $email = $request->input('mq_user_email');

        // 기본 유효성 검사
        if (empty($email)) {
            return response()->json([
                'available' => false,
                'message' => '이메일을 입력해주세요.'
            ]);
        }

        // 이메일 형식 검증
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'available' => false,
                'message' => '올바른 이메일 형식이 아닙니다.'
            ]);
        }

        // 현재 사용자의 이메일과 동일하면 사용 가능으로 처리
        if ($user && $user->mq_user_email === $email) {
            return response()->json([
                'available' => true,
                'message' => '현재 사용 중인 이메일입니다.'
            ]);
        }

        // 다른 사용자가 사용 중인지 확인
        $exists = Member::where('mq_user_email', $email)->exists();

        if ($exists) {
            return response()->json([
                'available' => false,
                'message' => '이미 사용 중인 이메일입니다.'
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => '사용 가능한 이메일입니다.'
        ]);
    }

    /**
     * 현재 비밀번호 확인 (AJAX)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCurrentPassword(Request $request)
    {
        $user = Auth::user();

        // SNS 계정 체크
        if ($user->mq_provider) {
            return response()->json([
                'valid' => false,
                'message' => 'SNS 로그인 계정은 비밀번호를 변경할 수 없습니다.'
            ]);
        }

        $currentPassword = $request->input('current_password');

        // 기본 유효성 검사
        if (empty($currentPassword)) {
            return response()->json([
                'valid' => false,
                'message' => '현재 비밀번호를 입력해주세요.'
            ]);
        }

        // 현재 비밀번호 확인
        if (!Hash::check($currentPassword, $user->mq_user_password)) {
            return response()->json([
                'valid' => false,
                'message' => '현재 비밀번호가 일치하지 않습니다.'
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => '현재 비밀번호가 확인되었습니다.'
        ]);
    }

    /**
     * 비밀번호 변경
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // SNS 계정 체크
        if ($user->mq_provider) {
            return back()->with('error', 'SNS 로그인 계정은 비밀번호를 변경할 수 없습니다.');
        }

        // Validation
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:50',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\W_]{8,50}$/',
                'confirmed',
                'different:current_password'
            ],
        ], [
            'current_password.required' => '현재 비밀번호를 입력해주세요.',
            'new_password.required' => '새 비밀번호를 입력해주세요.',
            'new_password.min' => '비밀번호는 최소 8자 이상이어야 합니다.',
            'new_password.max' => '비밀번호는 최대 50자까지 가능합니다.',
            'new_password.regex' => '비밀번호는 영문과 숫자를 필수로 포함해야 합니다.',
            'new_password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
            'new_password.different' => '새 비밀번호는 현재 비밀번호와 달라야 합니다.',
        ]);

        // 현재 비밀번호 확인
        if (!Hash::check($request->current_password, $user->mq_user_password)) {
            return back()->with('error', '현재 비밀번호가 일치하지 않습니다.');
        }

        // 비밀번호 업데이트 (Mutator가 자동으로 해시화)
        $user->update([
            'mq_user_password' => $request->new_password
        ]);

        return back()->with('success', '비밀번호가 성공적으로 변경되었습니다.');
    }

}