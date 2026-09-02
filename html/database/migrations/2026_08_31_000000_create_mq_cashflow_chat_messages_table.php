<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqCashflowChatMessagesTable extends Migration
{
    /**
     * 캐시플로우 챗봇의 활성 대화를 담는 테이블.
     *
     * 이전에는 대화 이력이 세션(cashflow_chat_history)에만 있었다. 세션은 브라우저를 닫거나
     * 만료되면 사라지고, 파일 세션이라 긴 답변 몇 개만 쌓여도 세션 파일이 부풀었다.
     * 그래서 활성 대화를 회원별로 이 테이블에 옮긴다.
     *
     * 설계 메모
     *
     * - 회원 한 명당 활성 대화 하나만 둔다. 대화방을 여러 개 두는 개념이 없으므로 conversation
     *   식별자를 따로 만들지 않았다. '새 대화' 는 해당 회원의 행을 지우는 것으로 끝난다.
     *   나중에 대화방 목록이 필요해지면 mq_conversation_idx 를 더하면 되고, 조회는 이미
     *   (member_idx, idx) 인덱스를 타므로 그때 컬럼만 앞에 끼우면 된다.
     *
     * - 순서는 mq_reg_date 가 아니라 idx 로 잡는다. 한 턴의 사용자/모델 발화는 같은 초에
     *   저장되기 때문에 시간만으로는 순서가 흔들린다.
     *
     * - 첨부 이미지 자체는 담지 않는다. base64 한 장이 수 MB 라 대화가 쌓이면 테이블이
     *   감당하지 못하고, 지난 이미지를 다시 모델에 보낼 이유도 없다. 대신 mq_has_image 로
     *   '이 발화에 이미지가 있었다' 는 사실만 남겨 화면 복원 때 표시한다.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('mq_cashflow_chat_messages')) {
            Schema::create('mq_cashflow_chat_messages', function (Blueprint $table) {
                $table->bigIncrements('idx')->comment('메시지 고유 식별자 (대화 순서로도 쓴다)');
                $table->unsignedInteger('member_idx')->comment('mq_member.idx 외래키');
                $table->enum('mq_role', ['user', 'model'])->comment('발화 주체 (user: 사용자, model: 챗봇)');
                $table->text('mq_message')->comment('발화 내용 (평문/마크다운)');
                $table->tinyInteger('mq_has_image')->default(0)->comment('이미지를 첨부한 발화인지 (0: 아니오, 1: 예)');
                $table->timestamp('mq_reg_date')->useCurrent()->comment('발화 시각');

                // 조회는 항상 '이 회원의 대화를 순서대로' 라서 두 컬럼을 한 인덱스로 묶는다.
                // member_idx 가 선두라 아래 외래키가 요구하는 인덱스도 이것으로 충족된다.
                $table->index(['member_idx', 'idx'], 'idx_cashflow_chat_member_seq');
                $table->foreign('member_idx', 'fk_cashflow_chat_member_idx')
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
        Schema::dropIfExists('mq_cashflow_chat_messages');
    }
}
