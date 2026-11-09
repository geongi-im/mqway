<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'kakao' => [
        'client_id' => env('KAKAO_RESTAPI_KEY'),
        'client_secret' => env('KAKAO_CLIENT_SECRET'),
        'redirect' => env('KAKAO_REDIRECT_URI'),
    ],

    'naver' => [
        'client_id' => env('NAVER_CLIENT_ID'),
        'client_secret' => env('NAVER_CLIENT_SECRET'),
        'redirect' => env('NAVER_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini
    |--------------------------------------------------------------------------
    |
    | 뉴스 스크랩 AI 분석에서 사용한다. config:cache 를 켜도 값이 살아있도록
    | env() 직접 호출 대신 이 설정을 경유해서 읽는다.
    |
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        // 모델명. 본문 이해 + 구조화 출력 + url_context 를 모두 지원하는 모델이어야 한다.
        'model' => env('GEMINI_API_MODEL', 'gemini-3.7-flash'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (앱 푸시)
    |--------------------------------------------------------------------------
    |
    | 서버 발송은 서비스 계정 키(JSON 파일)로 인증한다. 키 파일은 git 에 올리지 않고
    | 서버마다 storage/app/firebase/service-account.json 에 따로 둔다.
    |
    | web 항목은 브라우저의 Firebase JS SDK 에 그대로 넘기는 공개 설정값이다.
    | (비밀값이 아니다. 화면 소스에 노출되는 것이 정상)
    |
    */
    'firebase' => [
        // 서비스 계정 키 파일 경로. 상대 경로면 프로젝트 루트(html) 기준이다.
        'credentials' => env('FIREBASE_CREDENTIALS', 'storage/app/firebase/service-account.json'),

        'web' => [
            'apiKey'            => env('FIREBASE_API_KEY'),
            'authDomain'        => env('FIREBASE_AUTH_DOMAIN'),
            'projectId'         => env('FIREBASE_PROJECT_ID'),
            'storageBucket'     => env('FIREBASE_STORAGE_BUCKET'),
            'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
            'appId'             => env('FIREBASE_APP_ID'),
        ],

        // 웹 푸시 인증서 공개 키 (Firebase 콘솔 > 클라우드 메시징 > 웹 푸시 인증서)
        'vapid_key' => env('FIREBASE_VAPID_KEY'),

        // 외부 프로그램이 /api/push/send 를 호출할 때 X-Push-Key 헤더로 보내는 공유 키.
        // 비어 있으면 발송 API 는 항상 거절한다.
        'push_api_key' => env('PUSH_API_KEY'),
    ],

];
