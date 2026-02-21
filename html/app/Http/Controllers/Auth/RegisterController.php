<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\Member;
use App\Rules\ForbiddenWord;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'mq_user_id' => [
                'required',
                'string',
                'min:4',
                'max:20',
                'unique:mq_member,mq_user_id',
                new ForbiddenWord
            ],
            'mq_user_password' => [
                'required',
                'string',
                'min:8',
                'max:50',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\W_]{8,50}$/',
                'confirmed'
            ],
            'mq_user_name' => ['required', 'string', 'max:255', new ForbiddenWord],
            'mq_user_email' => ['required', 'string', 'email', 'max:255', 'unique:mq_member,mq_user_email'],
            'mq_birthday' => ['nullable', 'date', 'before:today'],
            'agree_terms' => ['required', 'accepted'],
            'agree_privacy' => ['required', 'accepted'],
        ], [
            'mq_user_id.required' => '아이디를 입력해주세요.',
            'mq_user_id.min' => '아이디는 최소 4자 이상이어야 합니다.',
            'mq_user_id.max' => '아이디는 최대 20자까지 가능합니다.',
            'mq_user_id.unique' => '이미 사용 중인 아이디입니다.',
            'mq_user_password.required' => '비밀번호를 입력해주세요.',
            'mq_user_password.min' => '비밀번호는 최소 8자 이상이어야 합니다.',
            'mq_user_password.max' => '비밀번호는 최대 50자까지 가능합니다.',
            'mq_user_password.regex' => '비밀번호는 영문과 숫자를 필수로 포함해야 합니다.',
            'mq_user_password.confirmed' => '비밀번호가 일치하지 않습니다.',
            'mq_user_name.required' => '이름을 입력해주세요.',
            'mq_user_email.required' => '이메일을 입력해주세요.',
            'mq_user_email.email' => '올바른 이메일 형식이 아닙니다.',
            'mq_user_email.unique' => '이미 사용 중인 이메일입니다.',
            'mq_birthday.date' => '올바른 날짜 형식이 아닙니다.',
            'mq_birthday.before' => '생년월일은 오늘 이전 날짜여야 합니다.',
            'agree_terms.required' => '이용약관에 동의해주세요.',
            'agree_terms.accepted' => '이용약관에 동의해주세요.',
            'agree_privacy.required' => '개인정보 수집 및 이용에 동의해주세요.',
            'agree_privacy.accepted' => '개인정보 수집 및 이용에 동의해주세요.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\Member
     */
    protected function create(array $data)
    {
        return Member::create([
            'mq_user_id' => $data['mq_user_id'],
            'mq_user_password' => $data['mq_user_password'], // Mutator가 자동으로 해시화
            'mq_user_name' => $data['mq_user_name'],
            'mq_user_email' => $data['mq_user_email'],
            'mq_birthday' => $data['mq_birthday'] ?? null,
            'mq_provider' => null,
            'mq_provider_id' => null,
            'mq_status' => 1,
            'mq_level' => 1,
            'mq_last_login_date' => now(),
        ]);
    }

    /**
     * 아이디 중복 체크
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserId(Request $request)
    {
        $userId = $request->input('mq_user_id');

        // 기본 유효성 검사
        if (empty($userId)) {
            return response()->json([
                'available' => false,
                'message' => '아이디를 입력해주세요.'
            ]);
        }

        // 아이디 형식 검증 (4~20자)
        if (strlen($userId) < 4 || strlen($userId) > 20) {
            return response()->json([
                'available' => false,
                'message' => '아이디는 4~20자여야 합니다.'
            ]);
        }

        // 금지 단어 체크
        $forbidden = new ForbiddenWord();
        if (!$forbidden->passes('mq_user_id', $userId)) {
            return response()->json([
                'available' => false,
                'message' => '사용할 수 없는 단어가 포함되어 있습니다.'
            ]);
        }

        // 중복 체크
        $exists = Member::where('mq_user_id', $userId)->exists();

        if ($exists) {
            return response()->json([
                'available' => false,
                'message' => '이미 사용 중인 아이디입니다.'
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => '사용 가능한 아이디입니다.'
        ]);
    }

    /**
     * 이메일 중복 체크
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request)
    {
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

        // 중복 체크
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
}
