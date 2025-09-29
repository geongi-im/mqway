<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MyPageController extends Controller
{
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

        // 임시 MQ 매핑 데이터 - 통일된 이미지와 텍스트 사용
        $mappingItems = [
            // 직업 카테고리
            ['id' => 1, 'category' => 'career', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 2, 'category' => 'career', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 3, 'category' => 'career', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 4, 'category' => 'career', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 5, 'category' => 'career', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            
            // 취미 카테고리
            ['id' => 6, 'category' => 'hobby', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 7, 'category' => 'hobby', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 8, 'category' => 'hobby', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 9, 'category' => 'hobby', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 10, 'category' => 'hobby', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],

            // 라이프스타일 카테고리
            ['id' => 11, 'category' => 'lifestyle', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 12, 'category' => 'lifestyle', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 13, 'category' => 'lifestyle', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 14, 'category' => 'lifestyle', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],

            // 교육 카테고리
            ['id' => 15, 'category' => 'education', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 16, 'category' => 'education', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 17, 'category' => 'education', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],

            // 금융 카테고리
            ['id' => 18, 'category' => 'financial', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 19, 'category' => 'financial', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 20, 'category' => 'financial', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],

            // 여행 카테고리  
            ['id' => 21, 'category' => 'travel', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 22, 'category' => 'travel', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음'],
            ['id' => 23, 'category' => 'travel', 'image' => 'https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?w=300&h=300&fit=crop', 'description' => '나의 꿈을 향한 첫걸음']
        ];

        // 카테고리 목록
        $categories = [
            'all' => '전체',
            'career' => '직업',
            'hobby' => '취미', 
            'lifestyle' => '라이프스타일',
            'education' => '교육',
            'financial' => '금융',
            'travel' => '여행'
        ];

        return view('mypage.mapping', compact('user', 'mappingItems', 'categories'));
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

}