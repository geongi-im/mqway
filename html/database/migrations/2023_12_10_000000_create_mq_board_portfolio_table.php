<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqBoardPortfolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_board_portfolio', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_title', 255);
            $table->integer('mq_portfolio_idx')->nullable();
            $table->string('mq_investor_code', 50);
            $table->string('mq_user_id', 100);
            $table->integer('mq_view_cnt')->default(0);
            $table->integer('mq_like_cnt')->default(0);
            $table->char('mq_status', 1)->default('Y');
            $table->timestamp('mq_reg_date')->useCurrent();
            $table->timestamp('mq_update_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_board_portfolio');
    }
} 