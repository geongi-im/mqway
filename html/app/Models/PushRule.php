<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * 앱 푸시 자동 발송 규칙 한 건. (관리자 페이지 > 앱 푸시관리)
 *
 * 대상 회원은 저장해 두지 않고 발송할 때마다 PushRuleService 가 조건으로 다시 고른다.
 * 이 클래스는 "언제 보내는가" 만 계산한다.
 */
class PushRule extends Model
{
    const TARGET_NO_SCRAP = 'no_scrap';
    const TARGET_NO_VISIT = 'no_visit';
    const TARGET_ALL = 'all';

    const FREQ_DAILY = 'daily';
    const FREQ_WEEKLY = 'weekly';
    const FREQ_MONTHLY = 'monthly';

    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    /**
     * 고를 수 있는 발송 시각 범위. 정보통신망법상 21시 ~ 다음 날 8시에는 광고성 정보를 보내려면
     * 별도 동의가 필요하므로 그 시간대는 아예 고를 수 없게 한다.
     */
    const SEND_TIME_MIN = '08:00';
    const SEND_TIME_MAX = '20:30';

    /**
     * 발송 시각부터 이 시간(분) 안에서만 보낸다. 스케줄러가 잠깐 멈췄다 돌아와도 놓치지 않게 여유를 두되,
     * 지난 시각으로 규칙을 새로 만들었을 때 엉뚱한 시각에 바로 나가지 않도록 짧게 둔다.
     * (20:30 + 30분 = 21:00 이라 야간 제한도 넘지 않는다)
     */
    const SEND_WINDOW_MINUTES = 30;

    /** 대상 조건 설명. {N} 은 mq_target_days 로 바뀐다. */
    const TARGET_LABELS = [
        self::TARGET_NO_SCRAP => '최근 {N}일 동안 뉴스 스크랩을 하지 않은 회원',
        self::TARGET_NO_VISIT => '최근 {N}일 동안 접속하지 않은 회원',
        self::TARGET_ALL      => '알림을 켠 회원 전체',
    ];

    const WEEKDAY_LABELS = [1 => '월', 2 => '화', 3 => '수', 4 => '목', 5 => '금', 6 => '토', 7 => '일'];

    protected $table = 'mq_push_rules';
    protected $primaryKey = 'idx';

    public $timestamps = false;

    protected $fillable = [
        'mq_name',
        'mq_target',
        'mq_target_days',
        'mq_frequency',
        'mq_weekdays',
        'mq_month_day',
        'mq_send_time',
        'mq_title',
        'mq_body',
        'mq_link',
        'mq_status',
        'mq_last_run_date',
        'mq_last_log_idx',
        'mq_reg_user_id',
        'mq_reg_date',
        'mq_update_date',
    ];

    protected $casts = [
        'mq_target_days'   => 'integer',
        'mq_month_day'     => 'integer',
        'mq_status'        => 'integer',
        'mq_last_run_date' => 'datetime',
        'mq_reg_date'      => 'datetime',
        'mq_update_date'   => 'datetime',
    ];

    public function lastLog()
    {
        return $this->belongsTo(PushLog::class, 'mq_last_log_idx', 'idx');
    }

    public function isOn()
    {
        return $this->mq_status === self::STATUS_ON;
    }

    /**
     * 고를 수 있는 발송 시각 목록 (30분 단위).
     *
     * @return string[]
     */
    public static function sendTimeOptions()
    {
        $options = [];
        $time = Carbon::createFromFormat('H:i', self::SEND_TIME_MIN);
        $max = Carbon::createFromFormat('H:i', self::SEND_TIME_MAX);
        while ($time->lte($max)) {
            $options[] = $time->format('H:i');
            $time->addMinutes(30);
        }
        return $options;
    }

    /**
     * @return int[] weekly 규칙의 요일 (1: 월 ~ 7: 일)
     */
    public function weekdayList()
    {
        if (!$this->mq_weekdays) {
            return [];
        }
        return array_map('intval', explode(',', $this->mq_weekdays));
    }

    public function targetLabel()
    {
        return str_replace('{N}', (string) $this->mq_target_days, self::TARGET_LABELS[$this->mq_target] ?? $this->mq_target);
    }

    public function scheduleLabel()
    {
        switch ($this->mq_frequency) {
            case self::FREQ_DAILY:
                return '매일 ' . $this->mq_send_time;
            case self::FREQ_WEEKLY:
                $days = array_map(function ($d) {
                    return self::WEEKDAY_LABELS[$d] ?? '';
                }, $this->weekdayList());
                return '매주 ' . implode(', ', $days) . '요일 ' . $this->mq_send_time;
            case self::FREQ_MONTHLY:
                $day = $this->mq_month_day >= 31 ? '말일' : $this->mq_month_day . '일';
                return '매월 ' . $day . ' ' . $this->mq_send_time;
        }
        return '';
    }

    /**
     * 그 날짜에 보내는 날이면 발송 시각, 아니면 null.
     *
     * @param  Carbon $date
     * @return Carbon|null
     */
    public function slotOn(Carbon $date)
    {
        $matches = false;
        switch ($this->mq_frequency) {
            case self::FREQ_DAILY:
                $matches = true;
                break;
            case self::FREQ_WEEKLY:
                $matches = in_array($date->dayOfWeekIso, $this->weekdayList(), true);
                break;
            case self::FREQ_MONTHLY:
                // 31일로 정하면 30일까지 있는 달은 30일, 2월은 28일(29일)에 보낸다.
                $matches = $date->day === min((int) $this->mq_month_day, $date->daysInMonth);
                break;
        }

        if (!$matches) {
            return null;
        }

        list($hour, $minute) = array_map('intval', explode(':', $this->mq_send_time));
        return $date->copy()->setTime($hour, $minute, 0);
    }

    /**
     * 지금 보내야 하는 회차의 발송 시각. 보낼 때가 아니면 null.
     *
     * @param  Carbon $now
     * @return Carbon|null
     */
    public function dueSlot(Carbon $now)
    {
        if (!$this->isOn()) {
            return null;
        }

        $slot = $this->slotOn($now);
        if ($slot === null || $now->lt($slot) || $now->gte($slot->copy()->addMinutes(self::SEND_WINDOW_MINUTES))) {
            return null;
        }

        // 이 회차를 이미 보냈다.
        if ($this->mq_last_run_date !== null && $this->mq_last_run_date->gte($slot)) {
            return null;
        }

        return $slot;
    }

    /**
     * 다음 발송 예정 시각. 꺼져 있으면 null.
     *
     * @param  Carbon|null $now
     * @return Carbon|null
     */
    public function nextRunAt(Carbon $now = null)
    {
        if (!$this->isOn()) {
            return null;
        }

        $now = $now ?: Carbon::now();

        // 지금 보내는 중인 회차가 있으면 그것.
        if ($slot = $this->dueSlot($now)) {
            return $slot;
        }

        // 월 1회 규칙도 찾을 수 있도록 두 달 남짓 앞까지 본다.
        $day = $now->copy()->startOfDay();
        for ($i = 0; $i < 63; $i++) {
            $slot = $this->slotOn($day);
            if ($slot !== null && $slot->gt($now)) {
                return $slot;
            }
            $day->addDay();
        }

        return null;
    }
}
