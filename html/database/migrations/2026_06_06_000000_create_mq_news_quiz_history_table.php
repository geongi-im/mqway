<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqNewsQuizHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mq_news_quiz_history')) {
            Schema::create('mq_news_quiz_history', function (Blueprint $table) {
                $table->bigIncrements('idx')->comment('내부 고유 ID');
                $table->string('mq_user_id', 100)->comment('회원 아이디');
                $table->unsignedBigInteger('mq_news_quiz_idx')->comment('뉴스 퀴즈 ID');
                $table->date('mq_news_date')->comment('뉴스 기준일/참여일');
                $table->string('mq_selected_answer', 20)->comment('선택 답변');
                $table->tinyInteger('mq_is_correct')->default(0)->comment('정답 여부 (0: 오답, 1: 정답)');
                $table->integer('mq_streak_days')->default(1)->comment('완료 시점 연속 참여일');
                $table->tinyInteger('mq_status')->default(1)->comment('상태 (0: 삭제, 1: 활성)');
                $table->timestamp('mq_reg_date')->useCurrent()->comment('등록일');
                $table->timestamp('mq_update_date')->nullable()->comment('수정일');

                $table->unique(['mq_user_id', 'mq_news_quiz_idx'], 'uq_mq_news_quiz_history_user_quiz');
                $table->index('mq_user_id', 'idx_mq_news_quiz_history_user');
                $table->index('mq_news_quiz_idx', 'idx_mq_news_quiz_history_quiz');
                $table->index('mq_news_date', 'idx_mq_news_quiz_history_date');
                $table->index(['mq_status', 'mq_news_date'], 'idx_mq_news_quiz_history_status_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_news_quiz_history');
    }
}
