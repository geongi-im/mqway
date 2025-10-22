<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqNewsScrapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_news_scrap', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_user_id', 100)->comment('작성자 ID');
            $table->string('mq_title', 500)->comment('뉴스 제목');
            $table->text('mq_url')->comment('뉴스 링크 URL');
            $table->text('mq_reason')->nullable()->comment('뉴스를 선택한 이유 (CKEditor)');
            $table->text('mq_new_terms')->nullable()->comment('새로 알게된 용어');
            $table->string('mq_thumbnail_url', 500)->nullable()->comment('뉴스 썸네일 이미지 URL');
            $table->tinyInteger('mq_status')->default(1)->comment('상태 (1: 활성, 0: 삭제)');
            $table->dateTime('mq_reg_date')->nullable()->comment('등록일');
            $table->dateTime('mq_update_date')->nullable()->comment('수정일');

            // 인덱스
            $table->index('mq_user_id');
            $table->index('mq_status');
            $table->index('mq_reg_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_news_scrap');
    }
}
