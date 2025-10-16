<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FindInfoController extends Controller
{
    /**
     * 회원정보 찾기 폼 표시
     */
    public function showFindInfoForm()
    {
        return view('auth.findinfo');
    }

    /**
     * 아이디 찾기
     * 이메일과 이름으로 가입한 계정의 아이디를 마스킹 처리하여 반환
     */
    public function findUserId(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
        ], [
            'name.required' => '이름을 입력해주세요.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
        ]);

        // 이메일과 이름으로 회원 찾기
        $member = Member::where('mq_user_email', $request->email)
                       ->where('mq_user_name', $request->name)
                       ->where('mq_status', 1) // 활성 계정만
                       ->first();

        if (!$member) {
            return response()->json([
                'message' => '입력하신 이메일과 이름에 일치하는 계정이 없습니다.'
            ], 404);
        }

        // Google OAuth 계정 확인
        if ($member->mq_provider === 'google') {
            return response()->json([
                'message' => 'Google 계정으로 가입하신 회원입니다. Google 로그인을 이용해주세요.'
            ], 400);
        }

        // 아이디 마스킹 처리 (가운데 30%를 * 처리)
        $maskedUserId = $this->maskUserId($member->mq_user_id);

        return response()->json([
            'masked_user_id' => $maskedUserId
        ]);
    }

    /**
     * 비밀번호 찾기 (임시 비밀번호 발급)
     * 아이디와 이메일이 일치하는 계정의 비밀번호를 임시 비밀번호로 변경하고 이메일 발송
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'email' => 'required|email',
        ], [
            'user_id.required' => '아이디를 입력해주세요.',
            'email.required' => '이메일을 입력해주세요.',
            'email.email' => '올바른 이메일 형식이 아닙니다.',
        ]);

        // 아이디와 이메일이 모두 일치하는 회원 찾기
        $member = Member::where('mq_user_id', $request->user_id)
                       ->where('mq_user_email', $request->email)
                       ->where('mq_status', 1) // 활성 계정만
                       ->first();

        if (!$member) {
            return response()->json([
                'message' => '입력하신 아이디와 이메일 정보가 일치하는 계정이 없습니다.'
            ], 404);
        }

        // Google OAuth 계정 확인
        if ($member->mq_provider === 'google') {
            return response()->json([
                'message' => 'Google 계정으로 가입하신 회원입니다. 비밀번호 찾기를 이용할 수 없습니다.'
            ], 400);
        }

        // 임시 비밀번호 생성 (영문+숫자 조합 10자리)
        $temporaryPassword = $this->generateTemporaryPassword();

        // 비밀번호 업데이트 (Member 모델의 mutator가 자동으로 해시 처리)
        $member->mq_user_password = $temporaryPassword;
        $member->save();

        // 이메일 발송 (모든 환경에서 실제 발송)
        try {
            // 임시 비밀번호 이메일 발송
            Mail::send('emails.temporary-password', [
                'userName' => $member->mq_user_name,
                'userId' => $member->mq_user_id,
                'temporaryPassword' => $temporaryPassword
            ], function ($message) use ($member) {
                $message->to($member->mq_user_email)
                       ->subject('[MQWAY] 임시 비밀번호 안내');
            });

            // 발송 기록 로그 (백업 및 디버깅 용도)
            \Log::info('임시 비밀번호 이메일 발송 완료', [
                'user_id' => $member->mq_user_id,
                'email' => $member->mq_user_email,
                'sent_at' => now()->toDateTimeString()
            ]);

            return response()->json([
                'message' => '임시 비밀번호가 이메일로 발송되었습니다. 이메일을 확인해주세요.'
            ]);
        } catch (\Exception $e) {
            // 이메일 발송 실패 시 로그 기록
            \Log::error('임시 비밀번호 이메일 발송 실패', [
                'user_id' => $member->mq_user_id,
                'email' => $member->mq_user_email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => '이메일 발송 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.'
            ], 500);
        }
    }

    /**
     * 아이디 마스킹 처리
     * 가운데 30%를 * 처리
     */
    private function maskUserId($userId)
    {
        $length = mb_strlen($userId);

        // 30% 계산 (최소 1자)
        $maskLength = max(1, (int)round($length * 0.3));

        // 시작 위치 계산 (가운데 기준)
        $startPos = (int)floor(($length - $maskLength) / 2);

        // 마스킹 처리
        $masked = mb_substr($userId, 0, $startPos) .
                  str_repeat('*', $maskLength) .
                  mb_substr($userId, $startPos + $maskLength);

        return $masked;
    }

    /**
     * 임시 비밀번호 생성
     * 영문 대소문자 + 숫자 조합 10자리
     */
    private function generateTemporaryPassword()
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';

        $all = $uppercase . $lowercase . $numbers;

        $password = '';

        // 최소 1개 이상의 대문자, 소문자, 숫자 포함 보장
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];

        // 나머지 7자리는 랜덤
        for ($i = 0; $i < 7; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // 문자 섞기
        return str_shuffle($password);
    }
}
