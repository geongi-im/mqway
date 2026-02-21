<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ForbiddenWord implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $forbiddenWords = config('forbidden_words.words', []);
        $forbiddenPatterns = config('forbidden_words.patterns', []);

        // 값이 없으면 통과 (required는 다른 규칙에서 처리)
        if (empty($value)) {
            return true;
        }

        $valueLower = strtolower(trim($value));

        // 1. 금지 단어 직접 포함 체크
        foreach ($forbiddenWords as $word) {
            if (stripos($valueLower, strtolower($word)) !== false) {
                return false;
            }
        }

        // 2. 정규표현식 패턴 체크
        foreach ($forbiddenPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '사용할 수 없는 단어가 포함되어 있습니다.';
    }
}
