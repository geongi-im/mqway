<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqCourseProgressTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_course_progress', function (Blueprint $table) {
            $table->increments('idx');  // Auto-increment primary key
            $table->unsignedInteger('member_idx');  // mq_member.idx 외래키
            $table->string('course_code', 50);  // 코스 코드 (예: 'l1', 'l2')
            $table->integer('step_number');  // 단계 번호 (1~8)
            $table->tinyInteger('is_completed')->default(0);  // 완료 여부 (0: 진행중, 1: 완료)
            $table->timestamp('mq_complete_date')->nullable();  // 완료 시간
            $table->timestamp('mq_update_date')->nullable();
            $table->timestamp('mq_reg_date')->useCurrent();

            // 유니크 제약: 한 사용자가 특정 코스의 특정 단계는 하나만 존재
            $table->unique(['member_idx', 'course_code', 'step_number'], 'unique_user_course_step');

            // 외래키 제약
            $table->foreign('member_idx')->references('idx')->on('mq_member')->onDelete('cascade');

            // 인덱스
            $table->index(['member_idx', 'course_code'], 'idx_member_course');
            $table->index(['course_code', 'step_number'], 'idx_course_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_course_progress');
    }
}
