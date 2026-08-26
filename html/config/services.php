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

];
