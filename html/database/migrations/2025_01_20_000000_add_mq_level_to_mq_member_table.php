<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMqLevelToMqMemberTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mq_member', function (Blueprint $table) {
            $table->tinyInteger('mq_level')->default(1)->after('mq_status')->comment('사용자 레벨 (1: 기본회원 ~ 10: 최고관리자)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mq_member', function (Blueprint $table) {
            $table->dropColumn('mq_level');
        });
    }
} 