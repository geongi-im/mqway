<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqPushTokensTable extends Migration
{
    /**
     * 회원이 앱 푸시(FCM) 알림을 허용한 기기 토큰.
     *
     * 설계 메모
     *
     * - 이 테이블이 '알림을 받는 기기' 명단이다. 발송할 때 여기서 켜져 있는 토큰을 골라
     *   mq_member 와 붙여 마케팅 정보 수신 동의 회원의 기기에만 하나씩 보낸다.
     *   (FCM 토픽은 쓰지 않는다. 명단이 구글 쪽에 생겨 우리가 대상을 거를 수 없기 때문)
     *
     * - 회원만 등록할 수 있다. 한 회원이 PC, 휴대폰처럼 기기를 여러 대 가질 수 있어
     *   mq_member 컬럼이 아니라 별도 테이블로 둔다. 회원이 지워지면 기기 토큰도 함께 지운다.
     *
     * - 토큰은 길이가 정해져 있지 않아 TEXT 로 두고, 중복 판별은 sha256 해시 컬럼의
     *   유니크 키로 한다. (mq_board_scrap 의 url_hash 와 같은 방식)
     *
     * - 웹뷰 하이브리드 앱이 붙으면 앱이 FCM SDK 로 받은 토큰을 같은 테이블에
     *   mq_platform = 'android' / 'ios' 로 넣는다.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mq_push_tokens')) {
            Schema::create('mq_push_tokens', function (Blueprint $table) {
                $table->bigIncrements('idx')->comment('토큰 고유 식별자');
                $table->text('mq_token')->comment('FCM 등록 토큰');
                $table->char('mq_token_hash', 64)->comment('mq_token 의 sha256 (중복 판별용)');
                $table->unsignedInteger('member_idx')->comment('mq_member.idx 외래키');
                $table->enum('mq_platform', ['web', 'android', 'ios'])->default('web')->comment('토큰을 받은 플랫폼');
                $table->string('mq_user_agent', 255)->nullable()->comment('등록 당시 브라우저 정보 (문의 대응용)');
                $table->tinyInteger('mq_status')->default(1)->comment('알림 상태 (1: 켜짐, 0: 꺼짐/만료)');
                $table->timestamp('mq_reg_date')->useCurrent()->comment('최초 등록 시각');
                $table->timestamp('mq_update_date')->nullable()->comment('마지막으로 다시 확인된 시각');

                $table->unique('mq_token_hash', 'uk_push_token_hash');
                $table->index(['member_idx', 'mq_status'], 'idx_push_token_member_status');
                $table->foreign('member_idx', 'fk_push_token_member_idx')
                      ->references('idx')->on('mq_member')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_push_tokens');
    }
}
