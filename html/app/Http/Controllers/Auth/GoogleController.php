<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $provider_id = $provider . '_' . $payload['sub'];

            // 기존 사용자 찾기 또는 새로 생성
            $user = User::where('email', $email)
                       ->orWhere('provider_id', $provider_id)
                       ->first();

            if ($user) {
                // 기존 사용자 정보 업데이트
                $user->update([
                    'name' => $name,
                    'provider' => $provider,
                    'provider_id' => $provider_id
                ]);
            } else {
                // 새 사용자 생성
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'provider' => $provider,
                    'provider_id' => $provider_id,
                    'password' => bcrypt(str_random(16))
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