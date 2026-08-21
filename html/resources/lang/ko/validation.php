<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | :attribute 은(는) 아래 attributes 배열에 등록된 이름으로 치환됩니다.
    | 등록되지 않은 필드는 컬럼명이 그대로 노출되므로, 화면에 노출되는
    | 필드는 attributes 에 한글 이름을 추가해주세요.
    |
    */

    'accepted' => ':attribute에 동의해주세요.',
    'active_url' => ':attribute이(가) 올바른 URL이 아닙니다.',
    'after' => ':attribute은(는) :date 이후 날짜여야 합니다.',
    'after_or_equal' => ':attribute은(는) :date 이후이거나 같은 날짜여야 합니다.',
    'alpha' => ':attribute은(는) 영문자만 사용할 수 있습니다.',
    'alpha_dash' => ':attribute은(는) 영문자, 숫자, 하이픈(-), 밑줄(_)만 사용할 수 있습니다.',
    'alpha_num' => ':attribute은(는) 영문자와 숫자만 사용할 수 있습니다.',
    'array' => ':attribute은(는) 배열이어야 합니다.',
    'before' => ':attribute은(는) :date 이전 날짜여야 합니다.',
    'before_or_equal' => ':attribute은(는) :date 이전이거나 같은 날짜여야 합니다.',
    'between' => [
        'numeric' => ':attribute은(는) :min에서 :max 사이여야 합니다.',
        'file' => ':attribute은(는) :min에서 :max KB 사이여야 합니다.',
        'string' => ':attribute은(는) :min자에서 :max자 사이여야 합니다.',
        'array' => ':attribute은(는) :min개에서 :max개 사이여야 합니다.',
    ],
    'boolean' => ':attribute 값이 올바르지 않습니다.',
    'confirmed' => ':attribute 확인이 일치하지 않습니다.',
    'date' => ':attribute이(가) 올바른 날짜 형식이 아닙니다.',
    'date_equals' => ':attribute은(는) :date와(과) 같은 날짜여야 합니다.',
    'date_format' => ':attribute이(가) :format 형식과 일치하지 않습니다.',
    'different' => ':attribute은(는) :other와(과) 달라야 합니다.',
    'digits' => ':attribute은(는) :digits자리 숫자여야 합니다.',
    'digits_between' => ':attribute은(는) :min자리에서 :max자리 숫자여야 합니다.',
    'dimensions' => ':attribute의 이미지 크기가 올바르지 않습니다.',
    'distinct' => ':attribute에 중복된 값이 있습니다.',
    'email' => ':attribute이(가) 올바른 이메일 형식이 아닙니다.',
    'ends_with' => ':attribute은(는) 다음 중 하나로 끝나야 합니다: :values',
    'exists' => '선택한 :attribute이(가) 올바르지 않습니다.',
    'file' => ':attribute은(는) 파일이어야 합니다.',
    'filled' => ':attribute을(를) 입력해주세요.',
    'gt' => [
        'numeric' => ':attribute은(는) :value보다 커야 합니다.',
        'file' => ':attribute은(는) :value KB보다 커야 합니다.',
        'string' => ':attribute은(는) :value자보다 길어야 합니다.',
        'array' => ':attribute은(는) :value개보다 많아야 합니다.',
    ],
    'gte' => [
        'numeric' => ':attribute은(는) :value보다 크거나 같아야 합니다.',
        'file' => ':attribute은(는) :value KB보다 크거나 같아야 합니다.',
        'string' => ':attribute은(는) :value자보다 길거나 같아야 합니다.',
        'array' => ':attribute은(는) :value개 이상이어야 합니다.',
    ],
    'image' => ':attribute은(는) 이미지 파일이어야 합니다.',
    'in' => '선택한 :attribute이(가) 올바르지 않습니다.',
    'in_array' => ':attribute이(가) :other에 존재하지 않습니다.',
    'integer' => ':attribute은(는) 정수여야 합니다.',
    'ip' => ':attribute은(는) 올바른 IP 주소여야 합니다.',
    'ipv4' => ':attribute은(는) 올바른 IPv4 주소여야 합니다.',
    'ipv6' => ':attribute은(는) 올바른 IPv6 주소여야 합니다.',
    'json' => ':attribute은(는) 올바른 JSON 문자열이어야 합니다.',
    'lt' => [
        'numeric' => ':attribute은(는) :value보다 작아야 합니다.',
        'file' => ':attribute은(는) :value KB보다 작아야 합니다.',
        'string' => ':attribute은(는) :value자보다 짧아야 합니다.',
        'array' => ':attribute은(는) :value개보다 적어야 합니다.',
    ],
    'lte' => [
        'numeric' => ':attribute은(는) :value보다 작거나 같아야 합니다.',
        'file' => ':attribute은(는) :value KB보다 작거나 같아야 합니다.',
        'string' => ':attribute은(는) :value자보다 짧거나 같아야 합니다.',
        'array' => ':attribute은(는) :value개를 초과할 수 없습니다.',
    ],
    'max' => [
        'numeric' => ':attribute은(는) :max 이하여야 합니다.',
        'file' => ':attribute은(는) :max KB 이하여야 합니다.',
        'string' => ':attribute은(는) 최대 :max자까지 가능합니다.',
        'array' => ':attribute은(는) :max개 이하여야 합니다.',
    ],
    'mimes' => ':attribute은(는) 다음 형식의 파일이어야 합니다: :values',
    'mimetypes' => ':attribute은(는) 다음 형식의 파일이어야 합니다: :values',
    'min' => [
        'numeric' => ':attribute은(는) :min 이상이어야 합니다.',
        'file' => ':attribute은(는) :min KB 이상이어야 합니다.',
        'string' => ':attribute은(는) 최소 :min자 이상이어야 합니다.',
        'array' => ':attribute은(는) :min개 이상이어야 합니다.',
    ],
    'not_in' => '선택한 :attribute이(가) 올바르지 않습니다.',
    'not_regex' => ':attribute 형식이 올바르지 않습니다.',
    'numeric' => ':attribute은(는) 숫자여야 합니다.',
    'password' => '비밀번호가 일치하지 않습니다.',
    'present' => ':attribute을(를) 입력해주세요.',
    'regex' => ':attribute 형식이 올바르지 않습니다.',
    'required' => ':attribute을(를) 입력해주세요.',
    'required_if' => ':other이(가) :value인 경우 :attribute을(를) 입력해주세요.',
    'required_unless' => ':other이(가) :values에 해당하지 않는 경우 :attribute을(를) 입력해주세요.',
    'required_with' => ':values을(를) 입력한 경우 :attribute도 입력해주세요.',
    'required_with_all' => ':values을(를) 모두 입력한 경우 :attribute도 입력해주세요.',
    'required_without' => ':values을(를) 입력하지 않은 경우 :attribute을(를) 입력해주세요.',
    'required_without_all' => ':values을(를) 모두 입력하지 않은 경우 :attribute을(를) 입력해주세요.',
    'same' => ':attribute과(와) :other이(가) 일치하지 않습니다.',
    'size' => [
        'numeric' => ':attribute은(는) :size이어야 합니다.',
        'file' => ':attribute은(는) :size KB여야 합니다.',
        'string' => ':attribute은(는) :size자여야 합니다.',
        'array' => ':attribute은(는) :size개여야 합니다.',
    ],
    'starts_with' => ':attribute은(는) 다음 중 하나로 시작해야 합니다: :values',
    'string' => ':attribute은(는) 문자열이어야 합니다.',
    'timezone' => ':attribute은(는) 올바른 시간대여야 합니다.',
    'unique' => '이미 사용 중인 :attribute입니다.',
    'uploaded' => ':attribute 업로드에 실패했습니다.',
    'url' => ':attribute 형식이 올바르지 않습니다.',
    'uuid' => ':attribute은(는) 올바른 UUID여야 합니다.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'mq_user_id' => '아이디',
        'mq_user_name' => '이름',
        'mq_user_email' => '이메일',
        'mq_user_password' => '비밀번호',
        'mq_birthday' => '생년월일',
        'mq_phone' => '휴대폰번호',
        'mq_profile_image' => '프로필 이미지',
        'current_password' => '현재 비밀번호',
        'new_password' => '새 비밀번호',
        'new_password_confirmation' => '새 비밀번호 확인',
        'agree_terms' => '이용약관',
        'agree_privacy' => '개인정보 수집 및 이용',
        'agree_marketing' => '마케팅 정보 수신 동의',
    ],

];
