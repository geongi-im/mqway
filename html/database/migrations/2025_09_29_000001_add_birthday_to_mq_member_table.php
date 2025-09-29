<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBirthdayToMqMemberTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mq_member', function (Blueprint $table) {
            $table->date('mq_birthday')->nullable()->after('mq_profile_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mq_member', function (Blueprint $table) {
            $table->dropColumn('mq_birthday');
        });
    }
}
