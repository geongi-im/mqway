<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqLifeSearchTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_life_search', function (Blueprint $table) {
            $table->increments('idx');
            $table->string('mq_user_id')->comment('회원 아이디');
            $table->string('mq_type', 20)->comment('유형(want_to_do: 하고싶은것, want_to_go: 가고싶은곳, want_to_share: 나누고싶은것)');
            $table->string('mq_category', 50)->comment('카테고리');
            $table->text('mq_content')->comment('항목');
            $table->integer('mq_price')->default(0)->comment('예상비용');
            $table->string('mq_expected_time', 50)->comment('예상소요시간');
            $table->timestamp('mq_reg_date')->useCurrent();
            $table->timestamp('mq_update_date')->nullable();

            $table->foreign('mq_user_id')
                  ->references('mq_user_id')
                  ->on('mq_member')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_life_search');
    }
}; 