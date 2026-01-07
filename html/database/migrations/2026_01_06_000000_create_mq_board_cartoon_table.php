<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqBoardCartoonTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('mq_board_cartoon')) {
            return;
        }

        Schema::create('mq_board_cartoon', function (Blueprint $table) {
            $table->increments('idx');
            $table->string('mq_title');
            $table->text('mq_content');
            $table->string('mq_category');
            $table->string('mq_user_id');
            $table->text('mq_image')->nullable();
            $table->text('mq_original_image')->nullable();
            $table->text('mq_thumbnail_image')->nullable();
            $table->text('mq_thumbnail_original')->nullable();
            $table->integer('mq_view_cnt')->default(0);
            $table->integer('mq_like_cnt')->default(0);
            $table->tinyInteger('mq_status')->default(1);
            $table->timestamp('mq_reg_date')->useCurrent();
            $table->timestamp('mq_update_date')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mq_board_cartoon');
    }
}
