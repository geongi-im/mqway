<?php

use App\Models\BoardScrap;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUrlHashToMqBoardScrapTable extends Migration
{
    /**
     * 중복 스크랩 방지용 URL 해시 + 회원별 활동 조회용 복합 인덱스
     *
     * mq_url 은 TEXT 라 그대로 인덱스를 걸 수 없고, 같은 기사도 http/https,
     * www/m 서브도메인, 추적 파라미터 때문에 문자열이 달라진다. 그래서 정규화한
     * URL 의 sha256 을 따로 보관하고 그걸로 비교한다.
     *
     * 유니크 제약을 걸지 않는 이유: 이 게시판은 소프트 삭제(mq_status=0)라
     * 지운 글이 행으로 남는다. 유니크를 걸면 삭제한 기사를 다시 못 올린다.
     * 중복 판정은 애플리케이션에서 mq_status=1 조건과 함께 처리한다.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mq_board_scrap', function (Blueprint $table) {
            if (!Schema::hasColumn('mq_board_scrap', 'mq_url_hash')) {
                $table->char('mq_url_hash', 64)->nullable()->after('mq_url')->comment('중복 검사용 정규화 URL 해시 (sha256)');
            }
        });

        // 회원별 중복 검사 조회용
        if (!$this->hasIndex('mq_board_scrap', 'mq_board_scrap_user_url_hash_index')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->index(['mq_user_id', 'mq_url_hash'], 'mq_board_scrap_user_url_hash_index');
            });
        }

        // 내 스크랩 목록 / 활동일 집계용 (mq_user_id + mq_status 로 좁히고 mq_reg_date 로 정렬)
        if (!$this->hasIndex('mq_board_scrap', 'mq_board_scrap_user_activity_index')) {
            Schema::table('mq_board_scrap', function (Blueprint $table) {
                $table->index(['mq_user_id', 'mq_status', 'mq_reg_date'], 'mq_board_scrap_user_activity_index');
            });
        }

        $this->backfillUrlHash();
    }

    /**
     * @return void
     */
    public function down()
    {
        foreach (['mq_board_scrap_user_url_hash_index', 'mq_board_scrap_user_activity_index'] as $indexName) {
            if ($this->hasIndex('mq_board_scrap', $indexName)) {
                Schema::table('mq_board_scrap', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }
        }

        Schema::table('mq_board_scrap', function (Blueprint $table) {
            if (Schema::hasColumn('mq_board_scrap', 'mq_url_hash')) {
                $table->dropColumn('mq_url_hash');
            }
        });
    }

    /**
     * 기존 글의 해시를 채운다.
     *
     * 게이미피케이션 집계는 소급하지 않지만 중복 검사는 소급이 필요하다.
     * 해시가 비어 있으면 예전에 올린 기사를 다시 올려도 중복으로 잡히지 않는다.
     *
     * @return void
     */
    private function backfillUrlHash()
    {
        DB::table('mq_board_scrap')
            ->select('idx', 'mq_url')
            ->whereNull('mq_url_hash')
            ->orderBy('idx')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    $hash = BoardScrap::urlHash($row->mq_url);

                    if ($hash === null) {
                        continue;
                    }

                    DB::table('mq_board_scrap')->where('idx', $row->idx)->update(['mq_url_hash' => $hash]);
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
