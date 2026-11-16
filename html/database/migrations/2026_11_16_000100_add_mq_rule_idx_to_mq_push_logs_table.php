<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 자동 발송 규칙이 보낸 이력에 어느 규칙이었는지 남긴다. (발송 이력 화면에 규칙 이름을 보여주려고)
 * 규칙을 지워도 이력은 남아야 하므로 외래키는 걸지 않는다.
 */
class AddMqRuleIdxToMqPushLogsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('mq_push_logs') && !Schema::hasColumn('mq_push_logs', 'mq_rule_idx')) {
            Schema::table('mq_push_logs', function (Blueprint $table) {
                $table->unsignedInteger('mq_rule_idx')->nullable()->after('mq_source')
                    ->comment('자동 발송 규칙 (mq_push_rules.idx, mq_source=rule 일 때만)');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('mq_push_logs', 'mq_rule_idx')) {
            Schema::table('mq_push_logs', function (Blueprint $table) {
                $table->dropColumn('mq_rule_idx');
            });
        }
    }
}
