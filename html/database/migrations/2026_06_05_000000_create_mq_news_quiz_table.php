<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMqNewsQuizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mq_news_quiz')) {
            Schema::create('mq_news_quiz', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';

                $table->bigIncrements('idx')->comment('내부 고유 ID');
                $table->date('mq_news_date')->comment('뉴스 기준일/선정일');
                $table->string('mq_company', 100)->nullable()->comment('신문사명');
                $table->string('mq_title', 500)->comment('뉴스 제목');
                $table->string('mq_source_url', 1000)->comment('뉴스 원문 링크');
                $table->string('mq_keyword', 100)->comment('선정 키워드');
                $table->text('mq_keyword_description')->comment('키워드 한줄설명');
                $table->json('mq_quiz_content')->comment('퀴즈 JSON(question, option_a, option_b, answer, explanation)');
                $table->text('mq_selection_reason')->nullable()->comment('선정 사유');
                $table->tinyInteger('mq_status')->default(1)->comment('상태 (0: 삭제, 1: 활성)');
                $table->dateTime('mq_reg_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('등록일');
                $table->dateTime('mq_update_date')->nullable()->comment('수정일');

                $table->index('mq_news_date', 'idx_mq_news_quiz_date');
                $table->index('mq_keyword', 'idx_mq_news_quiz_keyword');
                $table->index(['mq_status', 'mq_news_date'], 'idx_mq_news_quiz_status_date');
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
        Schema::dropIfExists('mq_news_quiz');
    }
}
