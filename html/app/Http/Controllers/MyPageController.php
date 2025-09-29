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

        // 임시 버킷리스트 데이터
        $bucketList = [
            [
                'id' => 1,
                'title' => '용돈 10만원 모으기',
                'category' => '저축',
                'target_amount' => 100000,
                'current_amount' => 65000,
                'progress' => 65,
                'deadline' => '2024-06-30',
                'status' => 'in_progress',
                'created_date' => '2024-01-15'
            ],
            [
                'id' => 2,
                'title' => '캐시플로우 게임 고수 되기',
                'category' => '학습',
                'target_amount' => null,
                'current_amount' => null,
                'progress' => 80,
                'deadline' => '2024-05-31',
                'status' => 'in_progress',
                'created_date' => '2024-02-01'
            ],
            [
                'id' => 3,
                'title' => '경제 상식 퀴즈 100점 달성',
                'category' => '학습',
                'target_amount' => null,
                'current_amount' => null,
                'progress' => 100,
                'deadline' => '2024-03-31',
                'status' => 'completed',
                'created_date' => '2024-02-15'
            ],
            [
                'id' => 4,
                'title' => '부모님께 경제 용어 10개 설명하기',
                'category' => '실습',
                'target_amount' => null,
                'current_amount' => null,
                'progress' => 30,
                'deadline' => '2024-07-15',
                'status' => 'in_progress',
                'created_date' => '2024-03-01'
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

        return view('mypage.index', compact('user', 'newsScrap', 'bucketList', 'likedContent'));
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
        ]);

        $user = Auth::user();

        $updateData = [
            'mq_user_name' => $request->mq_user_name,
            'mq_user_email' => $request->mq_user_email,
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

    public function bucketList()
    {
        $user = Auth::user();

        // 임시 버킷리스트 데이터
        $bucketList = [
            [
                'id' => 1,
                'title' => '용돈 10만원 모으기',
                'category' => '저축',
                'target_amount' => 100000,
                'current_amount' => 65000,
                'progress' => 65,
                'deadline' => '2024-06-30',
                'status' => 'in_progress',
                'created_date' => '2024-01-15'
            ],
            [
                'id' => 2,
                'title' => '캐시플로우 게임 고수 되기',
                'category' => '학습',
                'target_amount' => null,
                'current_amount' => null,
                'progress' => 80,
                'deadline' => '2024-05-31',
                'status' => 'in_progress',
                'created_date' => '2024-02-01'
            ],
            [
                'id' => 3,
                'title' => '경제 상식 퀴즈 100점 달성',
                'category' => '학습',
                'target_amount' => null,
                'current_amount' => null,
                'progress' => 100,
                'deadline' => '2024-03-31',
                'status' => 'completed',
                'created_date' => '2024-02-15'
            ],
            [
                'id' => 4,
                'title' => '부모님께 경제 용어 10개 설명하기',
                'category' => '실습',
                'target_amount' => null,
                'current_amount' => null,
                'progress' => 30,
                'deadline' => '2024-07-15',
                'status' => 'in_progress',
                'created_date' => '2024-03-01'
            ],
            [
                'id' => 5,
                'title' => '첫 번째 주식 투자하기',
                'category' => '투자',
                'target_amount' => 50000,
                'current_amount' => 20000,
                'progress' => 40,
                'deadline' => '2024-08-31',
                'status' => 'in_progress',
                'created_date' => '2024-03-10'
            ]
        ];

        return view('mypage.bucket-list', compact('user', 'bucketList'));
    }
}