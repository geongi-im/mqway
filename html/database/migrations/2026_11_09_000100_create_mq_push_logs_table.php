<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqPushLogsTable extends Migration
{
    /**
     * 앱 푸시 발송 이력. 발송 요청 한 번이 한 행이다.
     *
     * 설계 메모
     *
     * - 외부 수집 프로그램(cron)이 재시도하면서 같은 알림을 두 번 보내는 일을 막기 위해
     *   mq_dedupe_key 에 유니크 키를 건다. 키를 주지 않은 발송은 NULL 이라 제약을 받지 않는다.
     *
     * - 전부 실패한 키(failed)와 pending 으로 오래 멈춘 키는 다시 보낼 수 있다.
     *   한 기기라도 받았으면 sent 로 보고 다시 보내지 않는다.
     *
     * - 발송은 API 응답 뒤에 진행되므로, 결과(기기별 성공/실패 수)는 이 테이블에서 확인한다.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mq_push_logs')) {
            Schema::create('mq_push_logs', function (Blueprint $table) {
                $table->bigIncrements('idx')->comment('발송 이력 고유 식별자');
                $table->string('mq_dedupe_key', 191)->nullable()->comment('중복 발송 방지 키 (호출한 쪽이 정한다)');
                $table->string('mq_source', 50)->comment('발송 출처 (api: 외부 API 호출)');
                $table->string('mq_target', 100)->comment('발송 대상 (marketing_members: 마케팅 수신 동의 회원 전체)');
                $table->string('mq_title', 255)->comment('알림 제목');
                $table->text('mq_body')->nullable()->comment('알림 본문');
                $table->string('mq_link', 500)->nullable()->comment('알림을 눌렀을 때 열 주소');
                $table->unsignedInteger('mq_target_count')->default(0)->comment('발송 요청 시점의 대상 기기 수');
                $table->unsignedInteger('mq_success_count')->default(0)->comment('FCM 이 받은 기기 수');
                $table->unsignedInteger('mq_failure_count')->default(0)->comment('실패한 기기 수 (만료 토큰 포함)');
                $table->enum('mq_status', ['pending', 'sent', 'failed'])->default('pending')->comment('발송 상태');
                $table->text('mq_error')->nullable()->comment('실패 사유 (만료 토큰 외 첫 번째 오류)');
                $table->timestamp('mq_reg_date')->useCurrent()->comment('요청 시각');
                $table->timestamp('mq_send_date')->nullable()->comment('발송 완료 시각');

                $table->unique('mq_dedupe_key', 'uk_push_log_dedupe_key');
                $table->index('mq_reg_date', 'idx_push_log_reg_date');
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
        Schema::dropIfExists('mq_push_logs');
    }
}
