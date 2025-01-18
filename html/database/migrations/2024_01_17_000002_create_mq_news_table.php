<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqNewsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_news', function (Blueprint $table) {
            $table->increments('idx');  // Auto-increment primary key
            $table->string('mq_category', 20)->comment('뉴스 카테고리');
            $table->string('mq_title')->comment('뉴스 제목');
            $table->text('mq_content')->comment('뉴스 내용');
            $table->string('mq_company', 100)->comment('뉴스 발행사');
            $table->string('mq_source_url')->comment('원본 URL');
            $table->integer('mq_status')->default(1);  // 게시글 상태 (0: 삭제, 1: 활성)
            $table->timestamp('mq_reg_date')->useCurrent();  // 게시글 작성일
            $table->timestamp('mq_update_date')->nullable();  // 게시글 수정일
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_news');
    }
}; 