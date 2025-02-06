<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqLifeSearchSampleTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_life_search_sample', function (Blueprint $table) {
            $table->increments('idx');  // Auto-increment primary key
            $table->string('mq_s_category');  // 카테고리
            $table->text('mq_s_content');  // 목표 내용
            $table->integer('mq_s_price');  // 필요 금액
            $table->date('mq_s_target_date')->nullable();  // 목표 일자
            $table->timestamp('mq_reg_date')->useCurrent();  // 등록일
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_life_search_sample');
    }
} 