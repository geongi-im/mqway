<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneToMqMemberTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('mq_member') && !Schema::hasColumn('mq_member', 'mq_phone')) {
            Schema::table('mq_member', function (Blueprint $table) {
                $table->string('mq_phone', 20)->nullable()->after('mq_birthday');  // 휴대폰번호(선택, 숫자만 저장)
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mq_member') && Schema::hasColumn('mq_member', 'mq_phone')) {
            Schema::table('mq_member', function (Blueprint $table) {
                $table->dropColumn('mq_phone');
            });
        }
    }
}
