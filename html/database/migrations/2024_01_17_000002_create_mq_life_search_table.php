<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqLifeSearchTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_life_search', function (Blueprint $table) {
            $table->increments('idx');  // Auto-increment primary key
            $table->string('mq_user_id');  // 사용자 ID
            $table->string('mq_category');  // 카테고리
            $table->text('mq_content');  // 목표 내용
            $table->integer('mq_price');  // 필요 금액
            $table->date('mq_target_date')->nullable();  // 목표 일자
            $table->timestamp('mq_reg_date')->useCurrent();  // 등록일
            $table->timestamp('mq_update_date')->nullable();  // 수정일
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_life_search');
    }
} 