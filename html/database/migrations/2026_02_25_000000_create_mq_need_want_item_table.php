<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqNeedWantItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_need_want_item', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_name', 100)->comment('아이템 이름');
            $table->string('mq_description', 300)->comment('아이템 설명');
            $table->string('mq_image', 255)->nullable()->comment('이미지 경로');
            $table->tinyInteger('mq_is_active')->default(1)->comment('사용 여부 (0: 비활성, 1: 활성)');
            $table->timestamp('mq_reg_date')->useCurrent()->comment('등록일');
            $table->timestamp('mq_update_date')->nullable()->useCurrentOnUpdate()->comment('수정일');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_need_want_item');
    }
}
