<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqNewsTopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_news_top', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->date('mq_news_date')->comment('뉴스 날짜');
            $table->string('mq_company', 100)->comment('신문사명');
            $table->string('mq_title', 500)->comment('뉴스 제목');
            $table->text('mq_source_url')->comment('뉴스 원문 링크');
            $table->tinyInteger('mq_status')->default(1)->comment('상태 (0: 삭제, 1: 활성)');
            $table->dateTime('mq_reg_date')->nullable()->comment('등록일');
            $table->dateTime('mq_update_date')->nullable()->comment('수정일');

            // 인덱스
            $table->index('mq_news_date');
            $table->index(['mq_news_date', 'mq_status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_news_top');
    }
}
