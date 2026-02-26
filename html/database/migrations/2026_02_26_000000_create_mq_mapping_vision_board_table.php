<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqMappingVisionBoardTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_mapping_vision_board', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_user_id', 100)->comment('회원 아이디');
            $table->longText('canvas_data')->comment('Fabric.js canvas.toJSON() JSON 문자열');
            $table->timestamp('mq_reg_date')->useCurrent();
            $table->timestamp('mq_update_date')->nullable();

            $table->unique('mq_user_id');

            $table->foreign('mq_user_id')
                  ->references('mq_user_id')
                  ->on('mq_member')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_mapping_vision_board');
    }
}
