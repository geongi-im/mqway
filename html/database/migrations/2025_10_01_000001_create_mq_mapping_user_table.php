<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqMappingUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_mapping_user', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_user_id', 100)->comment('회원 아이디');
            $table->unsignedBigInteger('mi_idx')->comment('매핑 아이템 ID (mq_mapping_item의 idx)');
            $table->integer('mq_target_year')->comment('목표 연도');
            $table->timestamp('mq_reg_date')->useCurrent();
            $table->timestamp('mq_update_date')->nullable();

            $table->unique(['mq_user_id', 'mi_idx']);

            $table->foreign('mq_user_id')
                  ->references('mq_user_id')
                  ->on('mq_member')
                  ->onDelete('cascade');

            $table->foreign('mi_idx')
                  ->references('idx')
                  ->on('mq_mapping_item')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_mapping_user');
    }
}