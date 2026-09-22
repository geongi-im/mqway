<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropPublicDateFromMqBoardScrapTable extends Migration
{
    /**
     * 공개 전환 일시 컬럼 제거
     *
     * 게시판이 공개 목록 / 내 스크랩 두 갈래였을 때는 공개 목록만 "공개로 돌린 시점"
     * 순서로 보여주려고 mq_public_date 를 따로 들고 있었다. 목록을 하나로 합치면서
     * 정렬 기준이 작성일 하나로 통일되어 이 컬럼을 쓰는 코드가 남지 않는다.
     *
     * 공개 목록 전용 인덱스도 같은 이유로 걷어내고, 통합 목록 정렬에 맞는
     * (상태, 작성일) 인덱스로 바꾼다.
     *
     * @return void
     */
    public function up()
    {
        if ($this->hasIndex('mq_board_scrap', 'mq_board_scrap_public_list_index')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->dropIndex('mq_board_scrap_public_list_index');
            });
        }

        if (Schema::hasColumn('mq_board_scrap', 'mq_public_date')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->dropColumn('mq_public_date');
            });
        }

        if (!$this->hasIndex('mq_board_scrap', 'mq_board_scrap_list_index')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->index(['mq_status', 'mq_reg_date'], 'mq_board_scrap_list_index');
            });
        }
    }

    /**
     * 되돌리기
     *
     * 공개로 전환한 실제 시각은 복구할 수 없다. 컬럼을 다시 만들고 공개 상태인 글에
     * 한해 작성일로 채워 정렬이 깨지지 않을 정도만 맞춰 둔다.
     *
     * @return void
     */
    public function down()
    {
        if ($this->hasIndex('mq_board_scrap', 'mq_board_scrap_list_index')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->dropIndex('mq_board_scrap_list_index');
            });
        }

        if (!Schema::hasColumn('mq_board_scrap', 'mq_public_date')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->dateTime('mq_public_date')->nullable()->after('mq_is_public')->comment('공개로 전환한 일시');
            });

            DB::table('mq_board_scrap')
                ->where('mq_is_public', 1)
                ->update(['mq_public_date' => DB::raw('mq_reg_date')]);
        }

        if (!$this->hasIndex('mq_board_scrap', 'mq_board_scrap_public_list_index')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->index(['mq_is_public', 'mq_status', 'mq_public_date'], 'mq_board_scrap_public_list_index');
            });
        }
    }

    /**
     * 인덱스 존재 여부 확인 (MySQL)
     *
     * @param string $table
     * @param string $indexName
     * @return bool
     */
    private function hasIndex($table, $indexName)
    {
        return count(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName])) > 0;
    }
}
