<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'mq_user_id';
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'mq_user_password' => 'required|string',
        ], [
            'mq_user_id.required' => '아이디를 입력해주세요.',
            'mq_user_password.required' => '비밀번호를 입력해주세요.',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return [
            'mq_user_id' => $request->get('mq_user_id'),
            'password' => $request->get('mq_user_password'),
            'mq_status' => 1, // 활성 계정만 로그인 가능
        ];
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // 마지막 로그인 시간 업데이트
        $user->update(['mq_last_login_date' => now()]);
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
                // 이메일 중복 체크 - 중복되면 null로 저장
                $emailToSave = $email;
                if ($email && $this->checkEmailDuplicate($email)) {
                    $emailToSave = null;
                }

                // 새 사용자 생성
                $user = Member::create([
                    'mq_user_id' => $user_id,
                    'mq_user_name' => $name,
                    'mq_user_email' => $emailToSave,
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

    /**
     * Kakao OAuth 리다이렉트
     */
    public function redirectToKakao()
    {
        $clientId = config('services.kakao.client_id');
        $redirectUri = config('services.kakao.redirect');

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
        ];

        return redirect('https://kauth.kakao.com/oauth/authorize?' . http_build_query($params));
    }

    /**
     * Kakao OAuth 콜백 처리
     */
    public function handleKakaoCallback(Request $request)
    {
        try {
            if (!$request->code) {
                return redirect()->route('login')->with('error', '인증 코드가 없습니다.');
            }

            // Kakao Token 엔드포인트로 직접 액세스 토큰 요청
            $clientId = config('services.kakao.client_id');
            $clientSecret = config('services.kakao.client_secret');
            $redirectUri = config('services.kakao.redirect');

            $tokenResponse = $this->getKakaoAccessToken($request->code, $clientId, $clientSecret, $redirectUri);
            if (!isset($tokenResponse['access_token'])) {
                throw new Exception('액세스 토큰을 받지 못했습니다.');
            }

            // 액세스 토큰으로 사용자 정보 조회
            $userInfo = $this->getKakaoUserInfo($tokenResponse['access_token']);
            if (!$userInfo) {
                throw new Exception('사용자 정보를 가져올 수 없습니다.');
            }

            $provider_id = $userInfo['id'];
            $email = $userInfo['kakao_account']['email'] ?? null;
            // 카카오에서 닉네임을 가져올 수 없으므로 랜덤 사용자 이름 생성
            $name = '카카오' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            $provider = 'kakao';

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
                // 이메일 중복 체크 - 중복되면 null로 저장
                $emailToSave = $email;
                if ($email && $this->checkEmailDuplicate($email)) {
                    $emailToSave = null;
                }

                // 새 사용자 생성
                $user = Member::create([
                    'mq_user_id' => $user_id,
                    'mq_user_name' => $name,
                    'mq_user_email' => $emailToSave,
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
     * Kakao Access Token 획득
     */
    private function getKakaoAccessToken($code, $clientId, $clientSecret, $redirectUri)
    {
        $ch = curl_init('https://kauth.kakao.com/oauth/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'code' => $code
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Kakao 사용자 정보 조회
     */
    private function getKakaoUserInfo($accessToken)
    {
        $ch = curl_init('https://kapi.kakao.com/v2/user/me');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/x-www-form-urlencoded;charset=utf-8'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Naver OAuth 리다이렉트
     */
    public function redirectToNaver()
    {
        $clientId = config('services.naver.client_id');
        $redirectUri = config('services.naver.redirect');

        // CSRF 방지를 위한 state 토큰 생성
        $state = bin2hex(random_bytes(16));
        session(['naver_state' => $state]);

        $params = [
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
        ];

        return redirect('https://nid.naver.com/oauth2.0/authorize?' . http_build_query($params));
    }

    /**
     * Naver OAuth 콜백 처리
     */
    public function handleNaverCallback(Request $request)
    {
        try {
            // State 토큰 검증 (CSRF 방어)
            if (!$request->code || !$request->state) {
                return redirect()->route('login')->with('error', '인증 코드가 없습니다.');
            }

            $sessionState = session('naver_state');
            if ($request->state !== $sessionState) {
                return redirect()->route('login')->with('error', '잘못된 요청입니다.');
            }

            // 세션에서 state 제거
            session()->forget('naver_state');

            // Naver Token 엔드포인트로 직접 액세스 토큰 요청
            $clientId = config('services.naver.client_id');
            $clientSecret = config('services.naver.client_secret');
            $redirectUri = config('services.naver.redirect');

            $tokenResponse = $this->getNaverAccessToken($request->code, $clientId, $clientSecret, $request->state);
            if (!isset($tokenResponse['access_token'])) {
                throw new Exception('액세스 토큰을 받지 못했습니다.');
            }

            // 액세스 토큰으로 사용자 정보 조회
            $userInfo = $this->getNaverUserInfo($tokenResponse['access_token']);
            if (!$userInfo || !isset($userInfo['response'])) {
                throw new Exception('사용자 정보를 가져올 수 없습니다.');
            }

            $naverUser = $userInfo['response'];
            $provider_id = $naverUser['id'];
            $email = $naverUser['email'] ?? null;
            
            // 네이버에서 닉네임을 가져올 수 없으므로 랜덤 사용자 이름 생성
            $name = '네이버' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
            $provider = 'naver';

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
                // 이메일 중복 체크 - 중복되면 null로 저장
                $emailToSave = $email;
                if ($email && $this->checkEmailDuplicate($email)) {
                    $emailToSave = null;
                }

                // 새 사용자 생성
                $user = Member::create([
                    'mq_user_id' => $user_id,
                    'mq_user_name' => $name,
                    'mq_user_email' => $emailToSave,
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
     * Naver Access Token 획득
     */
    private function getNaverAccessToken($code, $clientId, $clientSecret, $state)
    {
        $ch = curl_init('https://nid.naver.com/oauth2.0/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'state' => $state
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * Naver 사용자 정보 조회
     */
    private function getNaverUserInfo($accessToken)
    {
        $ch = curl_init('https://openapi.naver.com/v1/nid/me');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    /**
     * 이메일 중복 체크
     *
     * @param string|null $email 체크할 이메일
     * @param string|null $excludeUserId 제외할 사용자 ID (자신의 이메일은 허용)
     * @return bool 중복이면 true, 아니면 false
     */
    private function checkEmailDuplicate($email, $excludeUserId = null)
    {
        if (empty($email)) {
            return false;
        }

        $query = Member::where('mq_user_email', $email);

        if ($excludeUserId) {
            $query->where('mq_user_id', '!=', $excludeUserId);
        }

        return $query->exists();
    }
}
