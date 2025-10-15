<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        // 임시 뉴스 스크랩 데이터
        $newsScrap = [
            [
                'id' => 1,
                'title' => '어린이를 위한 용돈 관리법',
                'summary' => '올바른 용돈 관리 습관을 기르는 방법에 대해 알아보세요.',
                'image' => '/images/news/sample1.jpg',
                'scraped_date' => '2024-03-15',
                'source' => '경제교육뉴스'
            ],
            [
                'id' => 2,
                'title' => '투자의 기초: 주식이란 무엇인가?',
                'summary' => '어린이도 이해할 수 있는 쉬운 주식 투자 설명',
                'image' => '/images/news/sample2.jpg',
                'scraped_date' => '2024-03-12',
                'source' => 'MQ경제뉴스'
            ],
            [
                'id' => 3,
                'title' => '저축의 힘: 복리의 마법',
                'summary' => '시간이 지날수록 커지는 돈의 비밀을 알아보세요.',
                'image' => '/images/news/sample3.jpg',
                'scraped_date' => '2024-03-10',
                'source' => '어린이경제신문'
            ]
        ];


        // 좋아요한 콘텐츠 (기존 데이터 활용)
        $likedContent = [];
        if ($user) {
            $likedContent = DB::table('mq_like_history')
                ->where('mq_user_id', $user->mq_user_id)
                ->orderBy('mq_reg_date', 'desc')
                ->get()
                ->groupBy('mq_board_name');
        }

        return view('mypage.index', compact('user', 'newsScrap', 'likedContent'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'mq_user_name' => 'required|string|max:255',
            'mq_user_email' => 'required|email|max:255',
            'mq_profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mq_birthday' => 'nullable|date',
        ]);

        $user = Auth::user();

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
                ->with('error', 'MQ 매핑을 진행하려면 생일 정보가 필요합니다. 프로필에서 생일을 입력해주세요.');
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
        $imageData = $request->input('imageData'); // Base64 이미지 데이터

        try {
            if ($action === 'add') {
                $miIdx = $itemId;

                // 커스텀 목표인 경우 mq_mapping_item에 먼저 저장
                if (str_starts_with($itemId, 'custom-')) {
                    $imagePath = null;

                    if ($imageData) {
                        // Base64 이미지를 파일로 저장
                        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                        $decodedImage = base64_decode($imageData);

                        $filename = 'mapping_custom_' . $user->mq_user_id . '_' . time() . '.png';
                        Storage::disk('public')->put('uploads/mapping/' . $filename, $decodedImage);
                        $imagePath = $filename;
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

                return response()->json(['success' => true, 'mi_idx' => $miIdx]);

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

    public function newsScrap()
    {
        $user = Auth::user();

        // 임시 뉴스 스크랩 데이터
        $newsScrap = [
            [
                'id' => 1,
                'title' => '어린이를 위한 용돈 관리법',
                'summary' => '올바른 용돈 관리 습관을 기르는 방법에 대해 알아보세요.',
                'image' => '/images/news/sample1.jpg',
                'scraped_date' => '2024-03-15',
                'source' => '경제교육뉴스'
            ],
            [
                'id' => 2,
                'title' => '투자의 기초: 주식이란 무엇인가?',
                'summary' => '어린이도 이해할 수 있는 쉬운 주식 투자 설명',
                'image' => '/images/news/sample2.jpg',
                'scraped_date' => '2024-03-12',
                'source' => 'MQ경제뉴스'
            ],
            [
                'id' => 3,
                'title' => '저축의 힘: 복리의 마법',
                'summary' => '시간이 지날수록 커지는 돈의 비밀을 알아보세요.',
                'image' => '/images/news/sample3.jpg',
                'scraped_date' => '2024-03-10',
                'source' => '어린이경제신문'
            ],
            [
                'id' => 4,
                'title' => '경제 뉴스 읽는 방법',
                'summary' => '복잡한 경제 뉴스를 쉽게 이해하는 팁',
                'image' => null,
                'scraped_date' => '2024-03-08',
                'source' => '경제교육뉴스'
            ],
            [
                'id' => 5,
                'title' => '청소년을 위한 첫 투자',
                'summary' => '안전한 투자로 시작하는 재테크 첫걸음',
                'image' => '/images/news/sample5.jpg',
                'scraped_date' => '2024-03-05',
                'source' => '청소년경제'
            ]
        ];

        return view('mypage.news-scrap', compact('user', 'newsScrap'));
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
        ];

        return $mapping[$boardName] ?? $boardName;
    }

}