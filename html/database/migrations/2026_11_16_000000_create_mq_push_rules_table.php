<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 관리자 페이지의 앱 푸시 자동 발송 규칙.
 *
 * 스케줄러(php artisan push:run-rules, 1분마다)가 발송 시각이 된 규칙의 대상 회원을 그때 다시 골라 보낸다.
 */
class CreateMqPushRulesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('mq_push_rules')) {
            return;
        }

        Schema::create('mq_push_rules', function (Blueprint $table) {
            $table->increments('idx')->comment('규칙 고유 식별자');
            $table->string('mq_name', 100)->comment('규칙 이름 (관리자용)');
            $table->string('mq_target', 30)->comment('대상 (no_scrap: N일 스크랩 안 함, no_visit: N일 미접속, all: 알림 켠 회원 전체)');
            $table->unsignedSmallInteger('mq_target_days')->nullable()->comment('no_scrap / no_visit 의 N일');
            $table->string('mq_frequency', 10)->comment('주기 (daily, weekly, monthly)');
            $table->string('mq_weekdays', 20)->nullable()->comment('weekly 일 때 요일 목록 (1: 월 ~ 7: 일, 쉼표 구분)');
            $table->unsignedTinyInteger('mq_month_day')->nullable()->comment('monthly 일 때 날짜 (그 달에 없는 날이면 말일)');
            $table->char('mq_send_time', 5)->comment('발송 시각 HH:MM (08:00 ~ 20:30)');
            $table->string('mq_title', 100)->comment('알림 제목');
            $table->string('mq_body', 500)->nullable()->comment('알림 본문 ({이름} 은 회원 이름으로 바뀐다)');
            $table->string('mq_link', 500)->nullable()->comment('알림을 눌렀을 때 열 사이트 경로');
            $table->tinyInteger('mq_status')->default(1)->comment('사용 여부 (1: 사용, 0: 꺼짐)');
            $table->timestamp('mq_last_run_date')->nullable()->comment('마지막 발송 시각');
            $table->unsignedBigInteger('mq_last_log_idx')->nullable()->comment('마지막 발송 이력 (mq_push_logs.idx)');
            $table->string('mq_reg_user_id', 100)->nullable()->comment('만든 관리자 아이디');
            $table->timestamp('mq_reg_date')->useCurrent()->comment('만든 시각');
            $table->timestamp('mq_update_date')->nullable()->comment('마지막 수정 시각');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mq_push_rules');
    }
}
