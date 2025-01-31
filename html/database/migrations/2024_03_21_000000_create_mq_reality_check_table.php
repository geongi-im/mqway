<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqRealityCheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_reality_check', function (Blueprint $table) {
            $table->bigIncrements('idx');
            $table->string('mq_category', 50)->comment('지출 항목');
            $table->integer('mq_expected_amount')->default(0)->comment('예상 금액');
            $table->integer('mq_actual_amount')->default(0)->comment('실제 금액');
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
        Schema::dropIfExists('mq_reality_check');
    }
} 