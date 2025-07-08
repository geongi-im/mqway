<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMqCashflowAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mq_cashflow_assets', function (Blueprint $table) {
            // 기본 정보
            $table->bigIncrements('idx')->comment('자산 고유 식별자');
            $table->unsignedBigInteger('mq_game_idx')->comment('게임 테이블 외래키');
            $table->enum('mq_asset_type', ['Stock', 'Fund', 'RealEstate', 'Collectible', 'Loan', 'Investment'])
                  ->comment('자산 유형 (Stock:주식, Fund:펀드, RealEstate:부동산, Collectible:수집품, Loan:대출, Investment:투자)');
            $table->string('mq_name', 255)->comment('자산명 (회사명, 부동산명 등)');
            $table->string('mq_symbol', 10)->nullable()->comment('주식/펀드 심볼 (AAPL, MSFT 등)');
            
            // 가격 정보
            $table->decimal('mq_purchase_price', 15, 2)->default(0)->comment('구매 가격 (부동산의 경우 총 구매가)');
            $table->decimal('mq_current_value', 15, 2)->default(0)->comment('현재 가치/시가');
            $table->decimal('mq_total_value', 15, 2)->default(0)->comment('총 자산 가치');
            $table->decimal('mq_down_payment', 15, 2)->default(0)->comment('계약금/초기 투자금 (부동산용)');
            
            // 수익 정보
            $table->decimal('mq_monthly_income', 15, 2)->default(0)->comment('월 수익 (임대료, 이자 등)');
            $table->decimal('mq_monthly_dividend', 15, 2)->default(0)->comment('월 배당금 (주식/펀드용)');
            
            // 주식/펀드 정보
            $table->integer('mq_shares')->default(0)->comment('보유 주식/펀드 수량');
            $table->decimal('mq_average_price', 15, 2)->default(0)->comment('평균 매입가');
            $table->decimal('mq_total_invested', 15, 2)->default(0)->comment('총 투자 금액');
            
            // 부동산 정보
            $table->string('mq_property_id', 255)->nullable()->comment('부동산 고유 ID (모기지 연결용)');
            
            // 시스템 필드
            $table->timestamp('mq_reg_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('자산 등록일시');
            $table->timestamp('mq_update_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('자산 최종 수정일시');
            
            // 외래키 및 인덱스
            $table->index('mq_game_idx', 'idx_game_idx'); // 외래키를 위한 인덱스
            $table->foreign('mq_game_idx', 'fk_assets_game_idx')->references('idx')->on('mq_cashflow_games')->onDelete('cascade');
            $table->index(['mq_game_idx', 'mq_asset_type'], 'idx_assets_game_type');
            $table->index('mq_symbol', 'idx_assets_symbol');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mq_cashflow_assets');
    }
}