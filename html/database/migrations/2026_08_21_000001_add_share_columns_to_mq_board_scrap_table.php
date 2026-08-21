<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddShareColumnsToMqBoardScrapTable extends Migration
{
    /**
     * 공개 게시판 전환용 컬럼 추가
     *
     * 기존 스크랩은 모두 사적으로 작성된 글이므로 mq_is_public 기본값은 0(나만보기).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mq_board_scrap', function (Blueprint $table) {
            if (!Schema::hasColumn('mq_board_scrap', 'mq_is_public')) {
                $table->tinyInteger('mq_is_public')->default(0)->after('mq_thumbnail_url')->comment('공개 여부 (1: 공개, 0: 나만보기)');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_public_date')) {
                $table->dateTime('mq_public_date')->nullable()->after('mq_is_public')->comment('공개로 전환한 일시 (공개 목록 정렬 기준)');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_view_cnt')) {
                $table->unsignedInteger('mq_view_cnt')->default(0)->after('mq_public_date')->comment('조회수');
            }
            if (!Schema::hasColumn('mq_board_scrap', 'mq_like_cnt')) {
                $table->unsignedInteger('mq_like_cnt')->default(0)->after('mq_view_cnt')->comment('좋아요 수');
            }
        });

        // 공개 목록 조회용 복합 인덱스
        if (!$this->hasIndex('mq_board_scrap', 'mq_board_scrap_public_list_index')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->index(['mq_is_public', 'mq_status', 'mq_public_date'], 'mq_board_scrap_public_list_index');
            });
        }

        // 공개 글인데 공개 일시가 비어있으면 등록일로 채움 (정렬 기준 누락 방지)
        DB::table('mq_board_scrap')
            ->where('mq_is_public', 1)
            ->whereNull('mq_public_date')
            ->update(['mq_public_date' => DB::raw('mq_reg_date')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ($this->hasIndex('mq_board_scrap', 'mq_board_scrap_public_list_index')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->dropIndex('mq_board_scrap_public_list_index');
            });
        }

        Schema::table('mq_board_scrap', function (Blueprint $table) {
            foreach (['mq_is_public', 'mq_public_date', 'mq_view_cnt', 'mq_like_cnt'] as $column) {
                if (Schema::hasColumn('mq_board_scrap', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
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
