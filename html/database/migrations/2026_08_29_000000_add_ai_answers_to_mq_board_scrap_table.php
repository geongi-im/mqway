<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAiAnswersToMqBoardScrapTable extends Migration
{
    /**
     * 파트7 질문에 대한 사용자 답변 컬럼 추가
     *
     * 질문(mq_ai_questions)은 AI가 만들고 사용자가 고치지 않는 값이 되었다.
     * 대신 각 질문에 사용자가 직접 쓴 답을 담을 자리가 필요해 컬럼을 나눈다.
     *
     * - mq_ai_answers : 질문과 같은 순서의 JSON 배열. 답을 안 쓴 칸은 빈 문자열로 남는다.
     *                   답변은 선택 항목이므로 전부 비어 있으면 NULL 로 저장한다.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mq_board_scrap', function (Blueprint $table) {
            if (!Schema::hasColumn('mq_board_scrap', 'mq_ai_answers')) {
                $table->text('mq_ai_answers')->nullable()->after('mq_ai_questions')->comment('질문에 대한 사용자 답변 (JSON 배열, 질문과 같은 순서)');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mq_board_scrap', function (Blueprint $table) {
            if (Schema::hasColumn('mq_board_scrap', 'mq_ai_answers')) {
                $table->dropColumn('mq_ai_answers');
            }
        });
    }
}
