<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqMappingItemTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_mapping_item', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_category', 50)->comment('카테고리 creation(창작), adventure(탐험), challenge(도전), growth(성장), experience(경험), custom(기타)');
            $table->text('mq_image')->nullable()->comment('저장된 이미지 파일명');
            $table->string('mq_description', 200)->comment('목표 설명');
            $table->tinyInteger('mq_status')->default(1)->comment('활성화 여부 (1:활성, 0:비활성)');
            $table->timestamp('mq_reg_date')->useCurrent();
            $table->timestamp('mq_update_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_mapping_item');
    }
}