<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqLikeHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('mq_like_history', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_user_id', 100);
            $table->string('mq_board_name', 50);
            $table->unsignedBigInteger('mq_board_idx');
            $table->timestamp('mq_reg_date')->useCurrent();
            $table->unique(['mq_user_id', 'mq_board_name', 'mq_board_idx']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mq_like_history');
    }
}
