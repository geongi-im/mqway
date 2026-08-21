<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        // 업로드 총량(post_max_size) 초과.
        // ValidatePostSize는 StartSession보다 먼저 실행되는 전역 미들웨어라
        // 세션 플래시를 쓸 수 없다. HTML 요청은 resources/views/errors/413.blade.php
        // 가 렌더링되고(HttpException 기본 동작), API 요청만 여기서 JSON으로 응답한다.
        if ($exception instanceof PostTooLargeException && $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => '업로드한 파일의 전체 크기가 너무 큽니다. 한 번에 총 '
                    . static::maxPostSizeLabel() . '까지 업로드할 수 있습니다.',
            ], 413);
        }

        return parent::render($request, $exception);
    }

    /**
     * php.ini의 post_max_size를 화면 표기용 문자열로 변환 (예: 40M -> 40MB)
     */
    public static function maxPostSizeLabel()
    {
        $raw = trim((string) ini_get('post_max_size'));

        if ($raw === '' || $raw === '0') {
            return '허용된 크기';
        }

        $unit = strtoupper(substr($raw, -1));
        $number = (float) $raw;

        if ($unit === 'G') {
            return rtrim(rtrim(number_format($number * 1024, 0), '0'), '.') . 'MB';
        }

        if ($unit === 'M') {
            return ((int) $number) . 'MB';
        }

        if ($unit === 'K') {
            return round($number / 1024) . 'MB';
        }

        // 단위 없는 바이트 표기
        return round($number / 1024 / 1024) . 'MB';
    }
}
