<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqMemberTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mq_member', function (Blueprint $table) {
            $table->increments('idx');  // Auto-increment primary key
            $table->string('mq_user_id')->unique();  // 유저 아이디
            $table->string('mq_user_name')->nullable();  // 유저 이름
            $table->string('mq_user_email')->nullable();  // 유저 이메일
            $table->string('mq_user_password')->nullable();  // 비밀번호 (SNS 로그인의 경우 null 가능)
            $table->string('mq_provider')->nullable();  // SNS 제공자 (google, apple 등)
            $table->string('mq_provider_id')->nullable();  // SNS 제공자의 고유 ID
            $table->timestamp('mq_last_login_date')->nullable();  // 마지막 로그인 일자(yyyy-mm-dd hh:mm:ss)
            $table->integer('mq_status')->default(1);  // 계정 상태 (0: 비활성, 1: 활성, 2: 해지)
            $table->timestamp('mq_reg_date')->useCurrent();  // 회원가입일자(yyyy-mm-dd hh:mm:ss)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mq_member');
    }
} 