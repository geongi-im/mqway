<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThumbnailFieldsToMqBoardResearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mq_board_research', function (Blueprint $table) {
            $table->text('mq_thumbnail_image')->nullable()->after('mq_original_image');
            $table->text('mq_thumbnail_original')->nullable()->after('mq_thumbnail_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mq_board_research', function (Blueprint $table) {
            $table->dropColumn(['mq_thumbnail_image', 'mq_thumbnail_original']);
        });
    }
}