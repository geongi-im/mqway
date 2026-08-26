<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAiColumnsToMqBoardScrapTable extends Migration
{
    /**
     * AI 뉴스 분석 결과 컬럼 추가
     *
     * 사람 입력(제목/링크/선택한 이유)은 그대로 두고,
     * [AI분석] 버튼이 채우는 4개 파트를 저장할 컬럼을 추가한다.
     *
     * - mq_ai_interpretation : 파트4 뉴스에 대한 짧은 해석
     * - mq_news_term         : 파트5 경제 용어 리스트 (JSON, 사용자 체크 상태 포함)
     * - mq_ai_outlook_short  : 파트6 단기 전망 (줄바꿈 구분 불릿)
     * - mq_ai_outlook_long   : 파트6 중장기 전망 (줄바꿈 구분 불릿)
     * - mq_ai_questions      : 파트7 내 경제상황에 맞는 질문 (JSON 배열)
     *
     * 기존 mq_new_terms(사람이 직접 쓴 용어 메모)는 과거 글 보존을 위해 남겨둔다.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mq_board_scrap', function (Blueprint $table) {
            if (!Schema::hasColumn('mq_board_scrap', 'mq_ai_interpretation')) {
                $table->text('mq_ai_interpretation')->nullable()->after('mq_new_terms')->comment('AI 뉴스 해석');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_news_term')) {
                $table->text('mq_news_term')->nullable()->after('mq_ai_interpretation')->comment('AI 경제 용어 리스트 (JSON: term/definition/context/checked)');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_ai_outlook_short')) {
                $table->text('mq_ai_outlook_short')->nullable()->after('mq_news_term')->comment('AI 단기 전망 (줄바꿈 구분)');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_ai_outlook_long')) {
                $table->text('mq_ai_outlook_long')->nullable()->after('mq_ai_outlook_short')->comment('AI 중장기 전망 (줄바꿈 구분)');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_ai_questions')) {
                $table->text('mq_ai_questions')->nullable()->after('mq_ai_outlook_long')->comment('AI 개인 점검 질문 (JSON 배열)');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_ai_model')) {
                $table->string('mq_ai_model', 60)->nullable()->after('mq_ai_questions')->comment('분석에 사용한 모델명');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_ai_source')) {
                $table->string('mq_ai_source', 20)->nullable()->after('mq_ai_model')->comment('본문 확보 경로 (crawl: 서버 크롤링, url_context: 모델 직접 열람)');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_ai_date')) {
                $table->dateTime('mq_ai_date')->nullable()->after('mq_ai_source')->comment('AI 분석 실행 일시');
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
            $columns = [
                'mq_ai_interpretation',
                'mq_news_term',
                'mq_ai_outlook_short',
                'mq_ai_outlook_long',
                'mq_ai_questions',
                'mq_ai_model',
                'mq_ai_source',
                'mq_ai_date',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('mq_board_scrap', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
