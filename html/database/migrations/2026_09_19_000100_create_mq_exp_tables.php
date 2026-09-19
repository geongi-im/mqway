<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqExpTables extends Migration
{
    /**
     * 회원 등급 / 경험치 테이블
     *
     * mq_exp_history 가 단일 진실 원천이고 mq_member_exp 는 화면 조회용 캐시다.
     * 캐시는 원장에서 언제든 통째로 다시 만들 수 있다 (ExpService::syncMember).
     *
     * mq_member.mq_level 은 권한 값이라 여기에 관여하지 않는다.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mq_exp_history')) {
            Schema::create('mq_exp_history', function (Blueprint $table) {
                $table->bigIncrements('idx')->comment('내부 고유 ID');
                $table->string('mq_user_id', 100)->comment('회원 아이디');
                $table->string('mq_action_code', 50)->comment('지급 사유 코드 (config/exp.php actions)');
                $table->string('mq_ref_key', 100)->comment('중복 지급 방지 키 (대상 idx 또는 날짜)');
                $table->integer('mq_exp')->default(0)->comment('지급 경험치');
                $table->dateTime('mq_reg_date')->comment('지급 일시');

                // 같은 행동에 두 번 주지 않는 것을 DB 가 보장한다.
                // 애플리케이션 조건문에 기대면 동시 요청에서 반드시 새어 나간다.
                $table->unique(['mq_user_id', 'mq_action_code', 'mq_ref_key'], 'uq_mq_exp_history_action');

                // 일일 상한 계산용. mq_reg_date 를 DATE() 로 감싸지 않고 범위 조건으로 쓴다.
                $table->index(['mq_user_id', 'mq_action_code', 'mq_reg_date'], 'idx_mq_exp_history_user_action_date');
                $table->index(['mq_user_id', 'mq_reg_date'], 'idx_mq_exp_history_user_date');
            });
        }

        if (!Schema::hasTable('mq_member_exp')) {
            Schema::create('mq_member_exp', function (Blueprint $table) {
                $table->string('mq_user_id', 100)->primary()->comment('회원 아이디');
                $table->unsignedInteger('mq_total_exp')->default(0)->comment('누적 경험치');
                $table->string('mq_grade_code', 20)->default('bronze')->comment('등급 코드 (config/exp.php grades)');
                $table->dateTime('mq_grade_date')->nullable()->comment('마지막 등급 상승 일시');
                $table->unsignedInteger('mq_current_streak')->default(0)->comment('현재 연속 접속일');
                $table->unsignedInteger('mq_best_streak')->default(0)->comment('최장 연속 접속일');
                $table->date('mq_last_visit_date')->nullable()->comment('마지막 접속일');
                $table->dateTime('mq_reg_date')->nullable()->comment('등록일');
                $table->dateTime('mq_update_date')->nullable()->comment('수정일');

                $table->index('mq_total_exp', 'idx_mq_member_exp_total');
            });
        }
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_member_exp');
        Schema::dropIfExists('mq_exp_history');
    }
}
