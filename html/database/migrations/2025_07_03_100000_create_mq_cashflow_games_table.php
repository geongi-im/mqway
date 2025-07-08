<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqCashflowGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_cashflow_games', function (Blueprint $table) {
            // 기본 정보
            $table->bigIncrements('idx')->comment('게임 고유 식별자');
            $table->string('mq_user_id', 50)->comment('게임을 플레이하는 사용자 ID');
            $table->string('mq_session_key', 255)->unique()->comment('게임 세션 고유 키 (UUID)');
            $table->string('mq_player_name', 255)->comment('플레이어 이름');
            $table->string('mq_profession', 255)->comment('선택한 직업 (의사, 엔지니어, 교사 등)');
            $table->text('mq_dream')->nullable()->comment('플레이어의 꿈/목표');
            $table->decimal('mq_dream_cost', 15, 2)->nullable()->comment('꿈 달성에 필요한 금액');
            $table->boolean('mq_game_started')->default(false)->comment('게임 시작 여부');
            
            // 재무 상태 정보
            $table->decimal('mq_cash', 15, 2)->default(0)->comment('현재 보유 현금');
            $table->decimal('mq_salary', 15, 2)->default(0)->comment('월 급여 (근로 소득)');
            $table->decimal('mq_passive_income', 15, 2)->default(0)->comment('월 패시브 인컴 (임대료, 배당금 등)');
            $table->decimal('mq_total_income', 15, 2)->default(0)->comment('월 총 수입 (급여 + 패시브 인컴)');
            $table->decimal('mq_total_expenses', 15, 2)->default(0)->comment('월 총 지출');
            $table->decimal('mq_monthly_cash_flow', 15, 2)->default(0)->comment('월 현금 흐름 (수입 - 지출)');
            $table->boolean('mq_has_child')->default(false)->comment('자녀 보유 여부');
            $table->integer('mq_children_count')->default(0)->comment('자녀 수');
            $table->decimal('mq_per_child_expense', 15, 2)->default(200)->comment('자녀 1명당 월 지출액');
            
            // 지출 내역 상세
            $table->decimal('mq_expenses_taxes', 15, 2)->default(0)->comment('월 세금');
            $table->decimal('mq_expenses_home_payment', 15, 2)->default(0)->comment('월 주택 대출 상환액');
            $table->decimal('mq_expenses_school_loan', 15, 2)->default(0)->comment('월 학자금 대출 상환액');
            $table->decimal('mq_expenses_car_loan', 15, 2)->default(0)->comment('월 자동차 대출 상환액');
            $table->decimal('mq_expenses_credit_card', 15, 2)->default(0)->comment('월 신용카드 결제액');
            $table->decimal('mq_expenses_retail', 15, 2)->default(0)->comment('월 소매 지출 (생활비)');
            $table->decimal('mq_expenses_other', 15, 2)->default(0)->comment('월 기타 지출 (모기지 상환액 등)');
            $table->decimal('mq_expenses_children', 15, 2)->default(0)->comment('월 자녀 관련 지출');
            
            // 시스템 필드
            $table->timestamp('mq_reg_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('게임 생성일시');
            $table->timestamp('mq_update_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('게임 최종 수정일시');
            
            // 인덱스
            $table->index('mq_user_id');
            $table->index('mq_session_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_cashflow_games');
    }
}