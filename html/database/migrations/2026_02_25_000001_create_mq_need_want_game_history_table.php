<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqNeedWantGameHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_need_want_game_history', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_user_id', 100)->nullable()->comment('회원 아이디 (비회원은 null)');
            $table->json('mq_answers')->comment('게임 응답 JSON [{item_idx, choice, reason}, ...]');
            $table->timestamp('mq_reg_date')->useCurrent()->comment('게임 참여 일자');

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
        Schema::dropIfExists('mq_need_want_game_history');
    }
}
