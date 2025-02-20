<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Google OAuth 리다이렉트
     */
    public function redirectToGoogle()
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        return redirect('https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
    }

    /**
     * Google OAuth 콜백 처리
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            if (!$request->code) {
                return redirect()->route('login')->with('error', '인증 코드가 없습니다.');
            }

            // Google Token 엔드포인트로 직접 액세스 토큰 요청
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            $redirectUri = config('services.google.redirect');

            $tokenResponse = $this->getGoogleAccessToken($request->code, $clientId, $clientSecret, $redirectUri);
            if (!isset($tokenResponse['id_token'])) {
                throw new Exception('ID 토큰을 받지 못했습니다.');
            }

            // ID 토큰 디코딩
            $payload = $this->decodeJWT($tokenResponse['id_token']);
            if (!$payload) {
                throw new Exception('Invalid ID token');
            }

            $email = $payload['email'];
            $name = $payload['name'];
            $provider = 'google';
            $provider_id = $payload['sub'];
            
            // 고유번호를 해시화하고 첫 10자리만 사용하여 user_id 생성
            $hashed_id = substr(md5($provider_id), 0, 10);
            $user_id = $provider . '_' . $hashed_id;

            // 기존 사용자 찾기 또는 새로 생성
            $user = Member::where('mq_user_id', $user_id)->first();

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
                    'mq_user_id' => $user_id,
                    'mq_user_name' => $name,
                    'mq_user_email' => $email,
                    'mq_provider' => $provider,
                    'mq_provider_id' => $provider_id,
                    'mq_status' => 1,
                    'mq_last_login_date' => now()
                ]);
            }

            Auth::login($user);
            
            // 리다이렉트 대신 스크립트 반환
            return response()->view('auth.callback', [
                'redirectUrl' => redirect()->intended()->getTargetUrl()
            ], 200);

        } catch (Exception $e) {
            report($e);
            return response()->view('auth.callback', ['error' => '로그인 처리 중 오류가 발생했습니다.'], 200);
        }
    }

    /**
     * Google Access Token 획득
     */
    private function getGoogleAccessToken($code, $clientId, $clientSecret, $redirectUri)
    {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * JWT 토큰 디코딩
     */
    private function decodeJWT($jwt)
    {
        $parts = explode('.', $jwt);
        if (count($parts) != 3) {
            return null;
        }

        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        return json_decode($payload, true);
    }
}
