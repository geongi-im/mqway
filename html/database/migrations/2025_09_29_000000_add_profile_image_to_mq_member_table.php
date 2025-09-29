<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileImageToMqMemberTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mq_member', function (Blueprint $table) {
            $table->string('mq_profile_image')->nullable()->after('mq_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mq_member', function (Blueprint $table) {
            $table->dropColumn('mq_profile_image');
        });
    }
}