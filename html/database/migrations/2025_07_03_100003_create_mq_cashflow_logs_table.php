<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqCashflowLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_cashflow_logs', function (Blueprint $table) {
            // 기본 정보
            $table->bigIncrements('idx')->comment('로그 고유 식별자');
            $table->unsignedBigInteger('mq_game_idx')->comment('게임 테이블 외래키');
            $table->text('mq_log_message')->comment('게임 진행 로그 메시지 (카드 구매, 자산 판매 등)');
            $table->enum('mq_log_type', ['info', 'success', 'warning', 'error', 'expense'])->default('info')
                  ->comment('로그 유형 (info:일반정보, success:성공, warning:경고, error:오류, expense:지출)');
            
            // 시스템 필드
            $table->timestamp('mq_reg_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('로그 생성일시');
            
            // 외래키 및 인덱스
            $table->index('mq_game_idx', 'idx_game_idx'); // 외래키를 위한 인덱스
            $table->foreign('mq_game_idx', 'fk_logs_game_idx')->references('idx')->on('mq_cashflow_games')->onDelete('cascade');
            $table->index(['mq_game_idx', 'mq_reg_date'], 'idx_logs_game_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_cashflow_logs');
    }
}