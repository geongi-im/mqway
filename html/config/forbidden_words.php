<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 금지 단어 목록
    |--------------------------------------------------------------------------
    |
    | 회원가입 시 사용할 수 없는 단어 목록입니다.
    | 아이디, 이름 등 다양한 필드에 적용됩니다.
    | 시스템, 관리자, 운영 관련 단어 및 부적절한 단어들을 포함합니다.
    |
    */

    'words' => [
        // 시스템 및 운영 관련
        'admin', 'administrator', 'root', 'system', 'master',
        'manager', 'host', 'owner', 'staff', 'crew',
        'moderator', 'operator', 'support', 'help',
        'webmaster',

        // MQWAY 플랫폼 관련
        'mqway', 'mqway_admin', 'mqway_system', 'mqway_manager',
        'mqway_support', 'mqway_team', 'official', 'notice',
        '공식', '운영자', '관리자', '시스템', '어드민', '운영',

        // 기술 및 개발 관련
        'api', 'developer', 'testing',
        'debug', 'bot', 'robot', 'script', 'hack',
        'hacker', 'cracker', 'exploit', 'malware', 'virus',

        // 권리 및 법적 관련
        'copyright', 'trademark', 'patent', 'license',
        '저작권', '상표', '특허', '면허', '허가',

        // 사회 및 정치 관련
        'government', 'gov', '정부', '대통령', '국회',
        '의회', '법원', '검찰', '경찰', 'police',

        // 욕설 및 비속어 (예시 - 필요에 따라 추가)
        '씨발', '개새끼', '병신', '미친', '젠장',
        'fuck', 'shit', 'bitch', 'ass', 'damn',

        // 성적/혐오 표현 (예시 - 필요에 따라 추가)
        // 필요시 관리자가 추가
    ],

    /*
    |--------------------------------------------------------------------------
    | 금지 패턴 (정규표현식)
    |--------------------------------------------------------------------------
    |
    | 특정 패턴을 가진 텍스트를 차단합니다.
    |
    */
    'patterns' => [
        // 연속된 같은 문자 (예: aaa111)
        '/(\w)\1{4,}/',
        // 특수문자로만 구성
        '/^[^\w가-힣]+$/u',
        // admin, root 등의 변형 (예: admin123, root_kr)
        '/admin|root|master/i',
    ],
];
