<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMqTargetUsersToMqPushLogsTable extends Migration
{
    /**
     * 회원 지정 발송(/api/push/send-users)의 대상 아이디를 남길 컬럼.
     *
     * mq_target 이 users 인 행에서만 쓰고, 전체 발송(marketing_members)은 NULL 이다.
     * 발송은 응답 뒤에 이 값을 다시 읽어 회원을 찾으므로 요청받은 아이디를 그대로 JSON 배열로 둔다.
     * "알림이 안 왔다" 문의가 오면 이 값과 발송 결과를 함께 본다.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('mq_push_logs') && !Schema::hasColumn('mq_push_logs', 'mq_target_users')) {
            Schema::table('mq_push_logs', function (Blueprint $table) {
                $table->mediumText('mq_target_users')->nullable()->after('mq_target')
                    ->comment('회원 지정 발송의 대상 아이디 JSON 배열 (mq_target=users 일 때만)');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('mq_push_logs', 'mq_target_users')) {
            Schema::table('mq_push_logs', function (Blueprint $table) {
                $table->dropColumn('mq_target_users');
            });
        }
    }
}
