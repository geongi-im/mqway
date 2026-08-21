<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarketingAgreeToMqMemberTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('mq_member') && !Schema::hasColumn('mq_member', 'mq_marketing_agree')) {
            Schema::table('mq_member', function (Blueprint $table) {
                // 마케팅 정보 수신동의 (선택 항목, 미동의가 기본값)
                $table->boolean('mq_marketing_agree')->default(0)->after('mq_phone');
                // 동의/철회 시점 (수신동의 이력 보관용, 미동의 시 null)
                $table->timestamp('mq_marketing_agree_date')->nullable()->after('mq_marketing_agree');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mq_member') && Schema::hasColumn('mq_member', 'mq_marketing_agree')) {
            Schema::table('mq_member', function (Blueprint $table) {
                $table->dropColumn(['mq_marketing_agree', 'mq_marketing_agree_date']);
            });
        }
    }
}
