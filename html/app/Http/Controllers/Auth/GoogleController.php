<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class GoogleController extends Controller
{
    public function handleCallback(Request $request)
    {
        try {
            if (!$request->credential) {
                return response()->json(['success' => false, 'message' => '잘못된 요청입니다.']);
            }

            // JWT 토큰 디코딩
            $jwt = $request->credential;
            $parts = explode('.', $jwt);
            if (count($parts) != 3) {
                throw new Exception('Invalid JWT format');
            }

            // JWT payload 디코딩
            $payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', $parts[1]))), true);
            
            if (!$payload) {
                throw new Exception('Invalid JWT payload');
            }

            // 필수 필드 확인
            if (!isset($payload['email']) || !isset($payload['name']) || !isset($payload['sub'])) {
                throw new Exception('Missing required fields in JWT payload');
            }

            $email = $payload['email'];
            $name = $payload['name'];
            $provider = 'google';
            $provider_id = $payload['sub'];  // 구글에서 제공하는 순수 고유번호
            
            // 고유번호를 해시화하고 첫 10자리만 사용하여 user_id 생성
            $hashed_id = substr(md5($provider_id), 0, 10);
            $user_id = $provider . '_' . $hashed_id;  // 예: google_a1b2c3d4e5

            // 기존 사용자 찾기 또는 새로 생성
            $user = Member::where('mq_user_email', $email)
                          ->orWhere('mq_user_id', $user_id)
                          ->first();

            if ($user) {
                // 기존 사용자 정보 업데이트
                $user->update([
                    'mq_user_name' => $name,
                    'mq_provider' => $provider,
                    'mq_provider_id' => $provider_id,
                    'mq_last_login_date' => now()
                ]);
            } else {
                // 새 사용자 생성
                $user = Member::create([
                    'mq_user_id' => $user_id,  // provider_해시화된고유번호 형식
                    'mq_user_name' => $name,
                    'mq_user_email' => $email,
                    'mq_provider' => $provider,
                    'mq_provider_id' => $provider_id,  // 순수 고유번호
                    'mq_status' => 1,
                    'mq_last_login_date' => now()
                ]);
            }

            Auth::login($user);

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => '서버 오류가 발생했습니다.']);
        }
    }
} 