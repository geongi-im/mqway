<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMqBoardResearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_board_research', function (Blueprint $table) {
            $table->increments('idx');
            $table->string('mq_title');
            $table->text('mq_content');
            $table->string('mq_category');
            $table->string('mq_user_id');
            $table->text('mq_image')->nullable();
            $table->text('mq_original_image')->nullable();
            $table->integer('mq_view_cnt')->default(0);
            $table->integer('mq_like_cnt')->default(0);
            $table->tinyInteger('mq_status')->default(1);
            $table->timestamp('mq_reg_date')->nullable();
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
        Schema::dropIfExists('mq_board_research');
    }
} 