<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use DateTimeZone;

class CustomizeFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $formatter = new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s.u',
                true,
                true
            );
            
            // 한국 시간대 설정
            $formatter->setTimezone(new DateTimeZone('Asia/Seoul'));
            $handler->setFormatter($formatter);
        }
    }
} 