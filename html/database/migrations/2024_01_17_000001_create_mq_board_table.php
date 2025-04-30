<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqBoardTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_board', function (Blueprint $table) {
            $table->increments('idx');  // Auto-increment primary key
            $table->string('mq_title');  // 게시글 제목
            $table->text('mq_content');  // 게시글 내용
            $table->text('mq_image')->nullable();  // 게시글 이미지 URL
            $table->text('mq_original_image')->nullable();  // 이미지 원본 이름
            $table->string('mq_user_id');  // 작성자
            $table->string('mq_category');
            $table->integer('mq_view_cnt')->default(0);  // 조회수
            $table->integer('mq_like_cnt')->default(0);  // 좋아요 수
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
        Schema::dropIfExists('mq_board');
    }
} 