<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqCashflowLiabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_cashflow_liabilities', function (Blueprint $table) {
            // 기본 정보
            $table->bigIncrements('idx')->comment('부채 고유 식별자');
            $table->unsignedBigInteger('mq_game_idx')->comment('게임 테이블 외래키');
            $table->enum('mq_liability_type', ['Retail', 'Mortgage', 'Loan', 'CreditCard', 'HomeLoan', 'SchoolLoan', 'CarLoan', 'Emergency'])
                  ->comment('부채 유형 (Mortgage:모기지, Loan:일반대출, CreditCard:신용카드, HomeLoan:주택대출, SchoolLoan:학자금대출, CarLoan:자동차대출, Emergency:응급대출)');
            $table->string('mq_name', 255)->comment('부채명 (은행명, 카드사명 등)');
            $table->decimal('mq_amount', 15, 2)->comment('부채 총액/대출 원금');
            $table->decimal('mq_monthly_payment', 15, 2)->comment('월 상환액');
            $table->string('mq_property_id', 255)->nullable()->comment('연결된 부동산 ID (모기지 전용)');
            
            // 응급 대출 추가 정보
            $table->decimal('mq_interest_rate', 5, 2)->nullable()->comment('연 이자율 (%) - 응급대출용');
            $table->decimal('mq_remaining_balance', 15, 2)->nullable()->comment('잔여 대출 잔액 - 응급대출용');
            
            // 시스템 필드
            $table->timestamp('mq_reg_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('부채 등록일시');
            $table->timestamp('mq_update_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('부채 최종 수정일시');
            
            // 외래키 및 인덱스
            $table->index('mq_game_idx', 'idx_game_idx'); // 외래키를 위한 인덱스
            $table->foreign('mq_game_idx', 'fk_liabilities_game_idx')->references('idx')->on('mq_cashflow_games')->onDelete('cascade');
            $table->index(['mq_game_idx', 'mq_liability_type'], 'idx_liabilities_game_type');
            $table->index('mq_property_id', 'idx_liabilities_property');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_cashflow_liabilities');
    }
}