<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 앱 푸시 발송 이력 한 건.
 */
class PushLog extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    protected $table = 'mq_push_logs';
    protected $primaryKey = 'idx';

    public $timestamps = false;

    protected $fillable = [
        'mq_dedupe_key',
        'mq_source',
        'mq_target',
        'mq_title',
        'mq_body',
        'mq_link',
        'mq_target_count',
        'mq_success_count',
        'mq_failure_count',
        'mq_status',
        'mq_error',
        'mq_reg_date',
        'mq_send_date',
    ];

    protected $casts = [
        'mq_target_count'  => 'integer',
        'mq_success_count' => 'integer',
        'mq_failure_count' => 'integer',
        'mq_reg_date'      => 'datetime',
        'mq_send_date'     => 'datetime',
    ];
}
