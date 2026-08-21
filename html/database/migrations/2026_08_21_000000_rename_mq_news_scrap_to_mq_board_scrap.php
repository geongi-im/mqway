<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameMqNewsScrapToMqBoardScrap extends Migration
{
    /**
     * 뉴스 스크랩이 마이페이지 전용에서 공개 게시판으로 바뀌면서
     * 다른 게시판(mq_board_content, mq_board_video ...)과 이름 규칙을 맞춘다.
     *
     * 데이터는 rename 으로 그대로 보존된다.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('mq_news_scrap') && !Schema::hasTable('mq_board_scrap')) {
            Schema::rename('mq_news_scrap', 'mq_board_scrap');
        }

        // rename 후에도 인덱스 이름은 예전 테이블명을 그대로 유지하므로 함께 정리한다
        $this->renameIndexes('mq_board_scrap', [
            'mq_news_scrap_mq_user_id_index' => 'mq_board_scrap_mq_user_id_index',
            'mq_news_scrap_mq_status_index' => 'mq_board_scrap_mq_status_index',
            'mq_news_scrap_mq_reg_date_index' => 'mq_board_scrap_mq_reg_date_index',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->renameIndexes('mq_board_scrap', [
            'mq_board_scrap_mq_user_id_index' => 'mq_news_scrap_mq_user_id_index',
            'mq_board_scrap_mq_status_index' => 'mq_news_scrap_mq_status_index',
            'mq_board_scrap_mq_reg_date_index' => 'mq_news_scrap_mq_reg_date_index',
        ]);

        if (Schema::hasTable('mq_board_scrap') && !Schema::hasTable('mq_news_scrap')) {
            Schema::rename('mq_board_scrap', 'mq_news_scrap');
        }
    }

    /**
     * 인덱스 이름 일괄 변경 (존재하는 것만, MySQL 5.7+)
     *
     * @param string $table
     * @param array  $map [기존 이름 => 새 이름]
     * @return void
     */
    private function renameIndexes($table, array $map)
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        foreach ($map as $from => $to) {
            if ($this->hasIndex($table, $from) && !$this->hasIndex($table, $to)) {
                DB::statement("ALTER TABLE `{$table}` RENAME INDEX `{$from}` TO `{$to}`");
            }
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
