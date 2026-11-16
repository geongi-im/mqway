<?php

namespace App\Console\Commands;

use App\Services\PushRuleService;
use Illuminate\Console\Command;

/**
 * 발송 시각이 된 앱 푸시 자동 규칙을 보낸다.
 *
 * Console\Kernel 이 1분마다 부른다. 스케줄러는 docker-compose 의 scheduler 서비스가 돌린다.
 * 같은 회차는 중복 방지 키(rule:{규칙}:{발송 시각})로 한 번만 나가므로 여러 번 불려도 안전하다.
 */
class RunPushRules extends Command
{
    protected $signature = 'push:run-rules {--dry-run : 보내지 않고 지금 보낼 규칙과 대상 수만 보여준다}';

    protected $description = '발송 시각이 된 앱 푸시 자동 규칙을 보낸다';

    public function handle(PushRuleService $rules)
    {
        $dryRun = (bool) $this->option('dry-run');
        $results = $rules->runDue(null, $dryRun);

        if (!$results) {
            if ($dryRun) {
                $this->line('지금 보낼 규칙이 없습니다.');
            }
            return 0;
        }

        foreach ($results as $r) {
            $this->line(sprintf(
                '[%s] #%d %s (회차 %s) 대상 회원 %d명, 기기 %d대',
                $r['result'],
                $r['rule']->idx,
                $r['rule']->mq_name,
                $r['slot']->format('Y-m-d H:i'),
                $r['members'],
                $r['target_count']
            ));
        }

        return 0;
    }
}
