<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachFileColumnsToMqBoardInsightsTable extends Migration
{
    /**
     * 투자 인사이트 첨부파일(PDF/PPT 등) 컬럼 추가
     *
     * 이미지 첨부(mq_image)와 같은 방식으로 저장 파일명 배열과 원본 파일명 배열을
     * 같은 순서로 JSON 저장한다. 실제 파일은 public 디스크가 아닌
     * storage/app/private/board_insights_files 에 두고 다운로드 라우트로만 내려준다.
     *
     * - mq_attach_file     : 저장 파일명 (JSON 배열, 32자 난수 + 확장자)
     * - mq_attach_original : 원본 파일명 (JSON 배열)
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mq_board_insights', function (Blueprint $table) {
            if (!Schema::hasColumn('mq_board_insights', 'mq_attach_file')) {
                $table->text('mq_attach_file')->nullable()->after('mq_original_image')->comment('첨부파일 저장명 (JSON 배열)');
            }
            if (!Schema::hasColumn('mq_board_insights', 'mq_attach_original')) {
                $table->text('mq_attach_original')->nullable()->after('mq_attach_file')->comment('첨부파일 원본명 (JSON 배열)');
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
        Schema::table('mq_board_insights', function (Blueprint $table) {
            foreach (['mq_attach_file', 'mq_attach_original'] as $column) {
                if (Schema::hasColumn('mq_board_insights', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
