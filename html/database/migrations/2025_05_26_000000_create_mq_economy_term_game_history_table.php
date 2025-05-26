<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqEconomyTermGameHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_economy_term_game_history', function (Blueprint $table) {
            $table->bigIncrements('idx'); // idx(인덱스) - bigIncrements는 unsignedBigInteger PK, auto-increment
            $table->string('mq_user_id', 100);
            $table->integer('mq_total_count'); // mq_total_count(제출 문제 총 갯수)
            $table->integer('mq_correct_count'); // mq_correct_count(맞춘 문제 갯수)
            $table->integer('mq_duration_time'); // mq_duration_time(맞춘데 걸린시간(초))
            $table->timestamp('mq_reg_date')->useCurrent(); // mq_reg_date(참여일자 yyyy-mm-dd hh:mm:ss) - 기본값으로 현재 시간
            
            // 인덱스 추가 (선택 사항이지만, 조회 성능 향상에 도움될 수 있습니다)
            $table->index('mq_user_id');
            $table->index('mq_reg_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_economy_term_game_history');
    }
}