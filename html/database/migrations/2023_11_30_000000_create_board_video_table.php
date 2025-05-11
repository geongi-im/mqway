<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_board_video', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_title', 255);
            $table->text('mq_content');
            $table->string('mq_category', 50)->nullable();
            $table->string('mq_user_id', 100);
            $table->integer('mq_view_cnt')->default(0);
            $table->integer('mq_like_cnt')->default(0);
            $table->json('mq_image')->nullable();
            $table->json('mq_original_image')->nullable();
            $table->text('mq_video_url')->nullable(); // 비디오 URL
            $table->timestamp('mq_reg_date')->nullable();
            $table->timestamp('mq_update_date')->nullable();
            $table->tinyInteger('mq_status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_board_video');
    }
} 