@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-primary">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-6">
            <!-- 카테고리 및 제목 영역 -->
            <div class="mb-4">
                <span class="inline-block px-3 py-1 {{ $categoryColors[$post->mq_investor_code] ?? 'bg-blue-100 text-blue-800' }} rounded-md text-sm font-medium">
                    {{ $post->mq_investor_code }}
                </span>
            </div>

            <!-- 제목 -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $post->mq_title }}</h1>

            <!-- 메타 정보 -->
            <div class="flex flex-wrap items-center text-gray-600 text-sm mb-8 gap-3">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ $post->mq_user_id }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ $post->mq_reg_date ? $post->mq_reg_date->format('Y.m.d H:i') : '' }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ number_format($post->mq_view_cnt) }}</span>
                </div>
            </div>

            <!-- 포트폴리오 대시보드 스타일의 콘텐츠 영역 -->
            <div class="portfolio-content mb-8">
                <!-- 기존 내용 대신 구조화된 콘텐츠 -->
                <div class="portfolio-dashboard">
                    <!-- 원본 HTML 내용을 Blade에 맞게 수정하여 표시 -->
                    <style>
                        /* 대시보드 스타일 */
                        .portfolio-dashboard * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }

                        .portfolio-dashboard {
                            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                            color: #1f2937;
                            line-height: 1.6;
                            overflow-x: hidden;
                        }

                        .portfolio-container {
                            margin: 0 auto;
                            padding: 20px 0;
                        }

                        /* 헤더 스타일 */
                        .portfolio-header {
                            text-align: center;
                            margin-bottom: 40px;
                            position: relative;
                            overflow: hidden;
                            padding: 40px 0 30px;
                            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.8), rgba(247, 247, 247, 0.9));
                            border-radius: 0 0 30px 30px;
                            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
                        }

                        .portfolio-header::before {
                            content: '';
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 5px;
                            background: linear-gradient(90deg, #ffd100, #ff8800);
                        }
                        
                        .investor-info {
                            margin-bottom: 15px;
                            padding: 0 15px;
                        }
                        
                        .investor-info.no-company h1 {
                            margin-bottom: 0;
                        }
                        
                        .quarter-info {
                            margin-top: 10px;
                        }

                        .portfolio-header h1 {
                            font-size: 2.5rem;
                            font-weight: 800;
                            color: #1f2937;
                            margin-bottom: 0;
                            position: relative;
                            z-index: 1;
                            letter-spacing: -0.5px;
                            text-shadow: 1px 1px 0 rgba(255, 209, 0, 0.3);
                            display: inline-block;
                            padding: 0 15px;
                            max-width: 90%;
                            word-break: break-word;
                            hyphens: auto;
                            line-height: 1.2;
                        }

                        .subtitle {
                            color: #4b5563;
                            font-size: 1.6rem;
                            position: relative;
                            z-index: 1;
                            font-weight: 600;
                            margin-bottom: 8px;
                            display: inline-block;
                            padding: 0 5px;
                            max-width: 90%;
                            word-break: break-word;
                            hyphens: auto;
                        }
                        
                        .subtitle::after {
                            content: '';
                            position: absolute;
                            bottom: 2px;
                            left: 0;
                            width: 100%;
                            height: 6px;
                            background-color: rgba(255, 209, 0, 0.2);
                            z-index: -1;
                        }

                        .period {
                            color: #4b5563;
                            font-size: 1.1rem;
                            position: relative;
                            z-index: 1;
                            background: rgba(255, 209, 0, 0.15);
                            display: inline-block;
                            padding: 6px 20px;
                            border-radius: 20px;
                            margin-top: 0;
                            border: 1px solid rgba(255, 209, 0, 0.3);
                            font-weight: 600;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                        }

                        /* 요약 카드 스타일 */
                        .summary-cards {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 25px;
                            margin-bottom: 50px;
                            max-width: 1200px;
                            margin-left: auto;
                            margin-right: auto;
                        }

                        @media (min-width: 768px) {
                            .summary-cards {
                                grid-template-columns: repeat(4, 1fr);
                            }
                        }

                        .summary-card {
                            background: #ffffff;
                            border: 1px solid #ffd100;
                            border-radius: 20px;
                            padding: 20px;
                            text-align: center;
                            position: relative;
                            overflow: hidden;
                            transition: all 0.3s ease;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                        }

                        .summary-card:hover {
                            transform: translateY(-5px);
                            border-color: #ffc700;
                            box-shadow: 0 10px 30px rgba(255, 209, 0, 0.2);
                        }

                        .card-icon {
                            font-size: 2rem;
                            margin-bottom: 15px;
                            filter: drop-shadow(0 0 10px rgba(255, 209, 0, 0.3));
                        }

                        .card-value {
                            font-size: 1.8rem;
                            font-weight: 700;
                            background: linear-gradient(135deg, #ffd100 0%, #ffb800 100%);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                            margin-bottom: 5px;
                        }
                        
                        .card-value.positive {
                            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                        }
                        
                        .card-value.negative {
                            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                        }

                        .card-label {
                            color: #6b7280;
                            font-size: 0.9rem;
                            text-transform: uppercase;
                            letter-spacing: 1px;
                        }

                        /* 차트 섹션 */
                        .chart-section {
                            background: #ffffff;
                            border: 1px solid #e5e7eb;
                            border-radius: 20px;
                            padding: 30px;
                            margin-bottom: 50px;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                            width: 100%;
                            max-width: 100%;
                            margin-left: auto;
                            margin-right: auto;
                            overflow: hidden;
                        }

                        .chart-header {
                            text-align: center;
                            margin-bottom: 30px;
                        }

                        .chart-title {
                            font-size: 1.8rem;
                            font-weight: 600;
                            color: #1f2937;
                            margin-bottom: 10px;
                        }

                        #portfolioChart {
                            width: 100%;
                            height: 400px;
                            margin: 0 auto;
                            text-align: center;
                        }

                        /* 필터링 탭 스타일 */
                        .filter-tabs {
                            margin-bottom: 30px;
                            background: #fff;
                            border-radius: 12px;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                            padding: 5px;
                            border: 1px solid #e5e7eb;
                        }

                        .filter-tabs .tab-btn {
                            position: relative;
                            transition: all 0.3s ease;
                            font-weight: 500;
                            cursor: pointer;
                            padding: 12px 24px;
                            font-size: 1rem;
                            border-radius: 8px;
                            min-width: 120px;
                        }

                        .filter-tabs .tab-btn:hover {
                            color: #1f2937 !important;
                            background-color: rgba(255, 209, 0, 0.08);
                        }

                        .filter-tabs .tab-btn.active {
                            color: #1f2937 !important;
                            border-color: #ffd100 !important;
                            font-weight: 600;
                            background-color: rgba(255, 209, 0, 0.15);
                            box-shadow: 0 2px 8px rgba(255, 209, 0, 0.2);
                        }

                        .filter-tabs .tab-btn:after {
                            content: '';
                            position: absolute;
                            bottom: -2px;
                            left: 10%;
                            width: 80%;
                            height: 3px;
                            background-color: #ffd100;
                            transform: scaleX(0);
                            transform-origin: right;
                            transition: transform 0.3s ease;
                        }

                        .filter-tabs .tab-btn:hover:after {
                            transform: scaleX(1);
                            transform-origin: left;
                        }

                        .filter-tabs .tab-btn.active:after {
                            transform: scaleX(1);
                        }

                        /* 주식 그리드 스타일 */
                        .stocks-grid {
                            display: grid;
                            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                            gap: 20px;
                            margin-bottom: 50px;
                        }

                        .stock-card {
                            background: #ffffff;
                            border: 1px solid #e5e7eb;
                            border-radius: 16px;
                            padding: 20px;
                            position: relative;
                            overflow: hidden;
                            transition: all 0.3s ease;
                            cursor: pointer;
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                        }

                        .stock-card::before {
                            content: '';
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 3px;
                            background: linear-gradient(90deg, #ffd100 0%, #ffb800 100%);
                            transform: scaleX(0);
                            transform-origin: left;
                            transition: transform 0.3s ease;
                        }

                        .stock-card:hover::before {
                            transform: scaleX(1);
                        }

                        .stock-card:hover {
                            transform: translateY(-3px);
                            border-color: #ffd100;
                            box-shadow: 0 8px 25px rgba(255, 209, 0, 0.15);
                        }

                        .stock-header {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            margin-bottom: 15px;
                        }

                        .stock-symbol {
                            font-size: 1.5rem;
                            font-weight: 700;
                            color: #1f2937;
                            text-decoration: underline;
                        }

                        .stock-name {
                            font-size: 0.85rem;
                            color: #6b7280;
                            margin-top: 2px;
                        }

                        .stock-percentage {
                            font-size: 1.2rem;
                            font-weight: 600;
                            padding: 5px 12px;
                            border-radius: 8px;
                            background: rgba(255, 209, 0, 0.1);
                            color: #92400e;
                            border: 1px solid #ffd100;
                        }

                        .stock-metrics {
                            display: grid;
                            grid-template-columns: repeat(2, 1fr);
                            gap: 15px;
                        }

                        .metric {
                            display: flex;
                            flex-direction: column;
                        }

                        .metric-label {
                            font-size: 0.75rem;
                            color: #9ca3af;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            margin-bottom: 3px;
                        }

                        .metric-value {
                            font-size: 1.1rem;
                            font-weight: 600;
                            color: #1f2937;
                        }

                        .metric-value.positive {
                            color: #059669;
                        }

                        .metric-value.negative {
                            color: #dc2626;
                        }

                        .activity-badge {
                            display: inline-flex;
                            align-items: center;
                            padding: 4px 10px;
                            border-radius: 6px;
                            font-size: 0.75rem;
                            font-weight: 600;
                            margin-top: 15px;
                        }

                        .activity-badge.add {
                            background: rgba(16, 185, 129, 0.1);
                            color: #059669;
                            border: 1px solid rgba(16, 185, 129, 0.3);
                        }

                        .activity-badge.reduce {
                            background: rgba(239, 68, 68, 0.1);
                            color: #dc2626;
                            border: 1px solid rgba(239, 68, 68, 0.3);
                        }

                        .activity-badge.buy {
                            background: rgba(59, 130, 246, 0.1);
                            color: #2563eb;
                            border: 1px solid rgba(59, 130, 246, 0.3);
                        }

                        /* 반응형 조정 */
                        @media (max-width: 768px) {
                            .portfolio-header h1 {
                                font-size: 1.8rem;
                            }
                            
                            .subtitle {
                                font-size: 1.2rem;
                            }
                            
                            .period {
                                font-size: 0.9rem;
                            }
                            
                            .card-value {
                                font-size: 1.5rem;
                            }
                            
                            .chart-title {
                                font-size: 1.4rem;
                            }
                            
                            #portfolioChart {
                                height: 300px;
                            }
                        }
                    </style>

                    <div class="portfolio-dashboard">
                        <div class="portfolio-container">
                            <!-- 포트폴리오 헤더 -->
                            <div class="portfolio-header">
                                <div class="investor-info">
                                    <h1>{{ $portfolioMeta->investor_name }}</h1>
                                </div>
                                <div class="quarter-info">
                                    <p class="period">{{ $portfolioMeta->portfolio_date }} 기준 포트폴리오</p>
                                </div>
                            </div>

                            <!-- 요약 카드 - 4x1 그리드 -->
                            <div class="summary-cards">
                                <div class="summary-card">
                                    <div class="card-icon">💰</div>
                                    <div class="card-value">${{ $portfolioMeta->portfolio_value ? number_format($portfolioMeta->portfolio_value / 1000000000, 1) . 'B' : 'N/A' }}</div>
                                    <div class="card-label">총 포트폴리오 가치</div>
                                </div>
                                <div class="summary-card">
                                    <div class="card-icon">📊</div>
                                    <div class="card-value">{{ $portfolioMeta->number_of_stocks ?? $portfolioDetails->count() }}</div>
                                    <div class="card-label">보유 종목 수</div>
                                </div>
                                <div class="summary-card">
                                    <div class="card-icon">📈</div>
                                    <div class="card-value {{ $portfolioMeta->portfolio_avg_return < 0 ? 'negative' : 'positive' }}">
                                        {{ $portfolioMeta->portfolio_avg_return ? number_format($portfolioMeta->portfolio_avg_return, 2) . '%' : 'N/A' }}
                                    </div>
                                    <div class="card-label">평균 수익률</div>
                                </div>
                                <div class="summary-card">
                                    <div class="card-icon">🏆</div>
                                    <div class="card-value">{{ $topHolding->ticker ?? 'N/A' }}</div>
                                    <div class="card-label">최대 보유 종목</div>
                                </div>
                            </div>

                            <!-- 포트폴리오 비중 도넛 차트 -->
                            <div class="chart-section">
                                <div class="chart-header">
                                    <h2 class="chart-title">포트폴리오 구성 비중</h2>
                                </div>
                                <div id="portfolioChart"></div>
                            </div>
                            
                            <!-- 필터링 탭 영역 -->
                            <div class="filter-tabs mb-6">
                                <div class="flex justify-center">
                                    <button type="button" 
                                            class="tab-btn text-gray-600 hover:text-cdark font-medium" 
                                            data-filter="all"
                                            id="tab-all">
                                        전체 종목
                                    </button>
                                    <button type="button" 
                                            class="tab-btn text-gray-600 hover:text-cdark font-medium" 
                                            data-filter="buy"
                                            id="tab-buy">
                                        매수/추가 종목
                                    </button>
                                    <button type="button" 
                                            class="tab-btn text-gray-600 hover:text-cdark font-medium" 
                                            data-filter="sell"
                                            id="tab-sell">
                                        매도 종목
                                    </button>
                                </div>
                            </div>

                            <!-- 주식 그리드 (단일 그리드) -->
                            <div class="stocks-grid" id="stocks-grid">
                                @foreach($portfolioDetails as $stock)
                                <div class="stock-card" 
                                     data-type="{{ !$stock->recent_activity_type ? 'all' : (strtolower($stock->recent_activity_type) === 'reduce' ? 'sell' : (strtolower($stock->recent_activity_type) === 'buy' || strtolower($stock->recent_activity_type) === 'add' || strtolower($stock->recent_activity_type) === 'new' ? 'buy' : 'all')) }}">
                                    <div class="stock-header">
                                        <div>
                                            <div class="stock-symbol"><a href="https://finance.yahoo.com/quote/{{ $stock->ticker }}" target="_blank">{{ $stock->ticker }}</a></div>
                                            <div class="stock-name">{{ $stock->stk_name }}</div>
                                        </div>
                                        <div class="stock-percentage">{{ number_format($stock->portfolio_rate, 2) }}%</div>
                                    </div>
                                    <div class="stock-metrics">
                                        <div class="metric">
                                            <span class="metric-label">가치</span>
                                            <span class="metric-value">${{ $stock->reported_value_amount ? number_format($stock->reported_value_amount / 1000000, 1) . 'M' : 'N/A' }}</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-label">변동률</span>
                                            <span class="metric-value {{ $stock->reported_price_rate < 0 ? 'negative' : 'positive' }}">
                                                {{ $stock->reported_price_rate ? ($stock->reported_price_rate > 0 ? '+' : '') . number_format($stock->reported_price_rate, 2) . '%' : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($stock->recent_activity_type)
                                    <div class="activity-badge {{ strtolower($stock->recent_activity_type) === 'reduce' ? 'reduce' : (strtolower($stock->recent_activity_type) === 'buy' || strtolower($stock->recent_activity_type) === 'new' ? 'buy' : 'add') }}">
                                        {{ strtoupper($stock->recent_activity_type) }} {{ $stock->recent_activity_value ? number_format($stock->recent_activity_value, 2) . '%' : '' }}
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 버튼 영역 -->
            <div class="flex justify-between items-center mt-10">
                <!-- 좌측 버튼 -->
                <a href="{{ route('board-portfolio.index') }}" 
                   class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                    목록
                </a>
                
                <!-- 우측 버튼 그룹 -->
                <div class="flex items-center gap-2">
                    @if(auth()->check() && auth()->user()->mq_user_id === $post->mq_user_id)
                        <a href="{{ route('board-portfolio.edit', $post->idx) }}" 
                           class="inline-flex items-center justify-center h-10 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-gray-700 text-sm">
                            수정
                        </a>
                        <form action="{{ route('board-portfolio.destroy', $post->idx) }}"
                              method="POST"
                              onsubmit="return confirmDelete()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center h-10 px-4 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-200 transition-all text-sm">
                                삭제
                            </button>
                        </form>
                    @endif
                    <button onclick="likePost(event, {{ $post->idx }})" 
                            class="inline-flex items-center justify-center gap-2 h-10 px-4 {{ $isLiked ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-200 transition-all group"
                            title="{{ auth()->check() ? '좋아요' : '로그인이 필요합니다' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span>{{ number_format($post->mq_like_cnt) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.4.3/echarts.min.js"></script>
<script>
function confirmDelete() {
    return confirm('정말 삭제하시겠습니까?');
}

async function likePost(event, idx) {
    event.preventDefault();
    
    @guest
        alert('로그인이 필요한 기능입니다.');
        return;
    @endguest
    
    try {
        const button = event.currentTarget;
        const response = await fetch(`/board-portfolio/${idx}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        });

        const data = await response.json();

        if (!data.success) {
            alert(data.message);
            return;
        }
        
        // 좋아요 수 업데이트
        const likeCountElements = document.querySelectorAll('button[onclick^="likePost"] span');
        likeCountElements.forEach(element => {
            element.textContent = new Intl.NumberFormat().format(data.likes);
        });
        
        // 버튼 스타일 변경 (좋아요 상태에 따라)
        if (data.isLiked) {
            button.classList.remove('bg-gray-100', 'text-gray-600');
            button.classList.add('bg-yellow-100', 'text-yellow-800');
        } else {
            button.classList.remove('bg-yellow-100', 'text-yellow-800');
            button.classList.add('bg-gray-100', 'text-gray-600');
        }
        
    } catch (error) {
        console.error('Error:', error);
        alert('좋아요 처리 중 오류가 발생했습니다.');
    }
}

// 차트 데이터 설정
const chartData = @json($chartData);

// 색상 자동 생성 함수
function generateColors(count) {
    // 기본 색상 테마 - 금융/투자 테마에 적합한 색상들 (확장)
    const baseColors = [
        // 황금/주황 계열
        { h: 45, s: 100, l: 50 },   // 황금색 #ffd700
        { h: 36, s: 100, l: 50 },   // 골드 #ffaa00
        { h: 27, s: 100, l: 50 },   // 주황색 #ff8800
        { h: 18, s: 100, l: 50 },   // 다크 오렌지 #ff5500
        
        // 빨간 계열
        { h: 9, s: 100, l: 50 },    // 밝은 빨강 #ff2200
        { h: 0, s: 100, l: 50 },    // 빨강 #ff0000
        { h: 354, s: 85, l: 47 },   // 선홍색
        
        // 분홍/자주 계열
        { h: 350, s: 80, l: 50 },   // 자주색 #d9365e
        { h: 340, s: 80, l: 50 },   // 핑크
        { h: 330, s: 80, l: 50 },   // 밝은 핑크 #d935a3
        { h: 320, s: 75, l: 50 },   // 매젠타
        
        // 보라 계열
        { h: 310, s: 70, l: 50 },   // 연보라
        { h: 300, s: 70, l: 50 },   // 보라색 #bf3fd9
        { h: 285, s: 70, l: 50 },   // 중간 보라
        { h: 270, s: 70, l: 50 },   // 진한 보라 #8c3fd9
        { h: 260, s: 70, l: 45 },   // 남보라
        
        // 파랑 계열
        { h: 250, s: 70, l: 50 },   // 남색
        { h: 240, s: 70, l: 50 },   // 파란색 #3f3fd9
        { h: 225, s: 80, l: 50 },   // 중간 파랑
        { h: 210, s: 90, l: 50 },   // 하늘색 #0f88d9
        { h: 195, s: 90, l: 50 },   // 밝은 청록
        
        // 청록/초록 계열
        { h: 180, s: 90, l: 45 },   // 청록색 #0d9999
        { h: 165, s: 90, l: 42 },   // 밝은 녹색
        { h: 150, s: 90, l: 40 },   // 진한 녹색 #0d9957
        { h: 135, s: 90, l: 40 },   // 중간 녹색
        { h: 120, s: 90, l: 40 },   // 녹색 #0d990d
        
        // 라임/노랑 계열
        { h: 105, s: 90, l: 45 },   // 연두색
        { h: 90, s: 90, l: 45 },    // 라임색 #87cc0a
        { h: 75, s: 90, l: 50 },    // 밝은 라임
        { h: 60, s: 90, l: 50 },    // 노란색 #e6e619
        
        // 갈색 계열
        { h: 30, s: 60, l: 40 },    // 갈색
        { h: 20, s: 70, l: 35 },    // 진한 갈색
        
        // 회색 계열 (채도를 낮게 설정)
        { h: 210, s: 10, l: 70 },   // 밝은 회색 (파랑빛)
        { h: 0, s: 0, l: 60 },      // 중간 회색
        { h: 210, s: 5, l: 40 }     // 어두운 회색 (파랑빛)
    ];
    
    // 생성된 색상을 저장할 배열
    const colors = [];
    
    // 더 많은 색상 생성을 위한 변형 계수
    const hueVariations = 12;       // 색조 변형 수
    const saturationSteps = 5;      // 채도 변형 단계 수
    const lightnessSteps = 7;       // 밝기 변형 단계 수
    
    // 종목 수에 따라 색상 생성
    for (let i = 0; i < count; i++) {
        // 기본 색상 선택 (baseColors 배열 내에서 순환)
        const baseColor = baseColors[i % baseColors.length];
        
        // 색상 변형을 위한 계수 계산
        const variationIndex = Math.floor(i / baseColors.length);
        
        // 기본 색상값 복사
        let h = baseColor.h;
        let s = baseColor.s;
        let l = baseColor.l;
        
        if (variationIndex > 0) {
            // 변형 방식 결정 (4가지 변형 패턴을 더 세분화)
            const variationType = variationIndex % 4;
            const variationLevel = Math.floor(variationIndex / 4) + 1;
            
            // 변형 1: 색조(Hue) 변형 - 주 색상에서 조금씩 틀어짐
            if (variationType === 0) {
                // 색조 변형 (주 색상을 중심으로 ±30도 범위에서 변형)
                const hueShift = ((variationLevel % hueVariations) - hueVariations/2) * (60/hueVariations);
                h = ((baseColor.h + hueShift) + 360) % 360;
            } 
            // 변형 2: 채도(Saturation) 변형 - 더 선명하거나 탁한 색상
            else if (variationType === 1) {
                // 채도 변형 (기본 채도를 중심으로 ±30% 범위에서 변형)
                const saturationMod = ((variationLevel % saturationSteps) - Math.floor(saturationSteps/2)) * (60/saturationSteps);
                s = Math.max(20, Math.min(100, baseColor.s + saturationMod));
            } 
            // 변형 3: 밝기(Lightness) 변형 - 더 밝거나 어두운 색상
            else if (variationType === 2) {
                // 밝기 변형 (기본 밝기를 중심으로 ±25% 범위에서 변형)
                const lightnessMod = ((variationLevel % lightnessSteps) - Math.floor(lightnessSteps/2)) * (50/lightnessSteps);
                l = Math.max(20, Math.min(80, baseColor.l + lightnessMod));
            } 
            // 변형 4: 복합 변형 - 색조, 채도, 밝기 모두 변형
            else {
                // 복합 변형 (색조, 채도, 밝기 모두에 미묘한 변화)
                const complexLevel = variationLevel % 10;
                
                // 색조 미세 변형
                const hueComplex = ((complexLevel % 5) * 7 - 14) % 360;
                h = ((baseColor.h + hueComplex) + 360) % 360;
                
                // 채도 미세 변형
                const satComplex = ((complexLevel % 3) - 1) * 10;
                s = Math.max(30, Math.min(100, baseColor.s + satComplex));
                
                // 밝기 미세 변형
                const lightComplex = ((complexLevel % 7) - 3) * 5;
                l = Math.max(25, Math.min(75, baseColor.l + lightComplex));
            }
        }
        
        // HSL을 HEX 색상 코드로 변환
        colors.push(hslToHex(h, s, l));
    }
    
    return colors;
}

// HSL 색상 모델을 HEX 색상 코드로 변환하는 함수
function hslToHex(h, s, l) {
    s /= 100;
    l /= 100;
    
    const c = (1 - Math.abs(2 * l - 1)) * s;
    const x = c * (1 - Math.abs((h / 60) % 2 - 1));
    const m = l - c / 2;
    
    let r, g, b;
    
    if (0 <= h && h < 60) {
        [r, g, b] = [c, x, 0];
    } else if (60 <= h && h < 120) {
        [r, g, b] = [x, c, 0];
    } else if (120 <= h && h < 180) {
        [r, g, b] = [0, c, x];
    } else if (180 <= h && h < 240) {
        [r, g, b] = [0, x, c];
    } else if (240 <= h && h < 300) {
        [r, g, b] = [x, 0, c];
    } else {
        [r, g, b] = [c, 0, x];
    }
    
    // RGB 값을 HEX 코드로 변환
    const toHex = (value) => {
        const hex = Math.round((value + m) * 255).toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    };
    
    return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
}

// 카드 호버 효과
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.stock-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // 도넛 차트 초기화
    initDonutChart();
    
    // 탭 전환 기능 초기화
    initTabSystem();
});

// 도넛 차트 초기화
function initDonutChart() {
    const chartDom = document.getElementById('portfolioChart');
    const myChart = echarts.init(chartDom);

    // 실제 데이터 사용
    const chartItems = chartData.map(item => ({
        value: item.value,
        name: item.name,
        itemStyle: {
            borderRadius: 5,
            borderWidth: 2,
            borderColor: '#ffffff'
        }
    }));

    const option = {
        backgroundColor: 'transparent',
        tooltip: {
            trigger: 'item',
            formatter: '{b}: {c}%',
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: '#ffd100',
            borderWidth: 1,
            textStyle: {
                color: '#1f2937'
            },
            extraCssText: 'box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);'
        },
        legend: {
            type: 'scroll',
            orient: 'horizontal',
            left: 'center',
            bottom: '5%',
            itemGap: 12,
            pageButtonPosition: 'end',
            pageIconSize: [12, 12],
            pageIconColor: '#ffd100',
            pageIconInactiveColor: '#e5e7eb',
            pageTextStyle: {
                color: '#1f2937',
                fontSize: 10
            },
            textStyle: {
                color: '#4b5563',
                fontSize: 12
            },
            icon: 'circle',
            formatter: function (name) {
                const item = chartItems.find(d => d.name === name);
                return `${name}  ${item.value}%`;
            }
        },
        series: [
            {
                name: '포트폴리오 비중',
                type: 'pie',
                radius: ['40%', '70%'],
                center: ['50%', '50%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 10,
                    borderColor: '#ffffff',
                    borderWidth: 2
                },
                label: {
                    show: false,
                    position: 'center'
                },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 20,
                        fontWeight: 'bold',
                        color: '#1f2937',
                        formatter: function(params) {
                            return `${params.name}\n${params.value}%`;
                        }
                    },
                    itemStyle: {
                        shadowBlur: 20,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(255, 209, 0, 0.3)'
                    }
                },
                labelLine: {
                    show: false
                },
                data: chartItems,
                color: generateColors(chartItems.length),
                animationType: 'scale',
                animationEasing: 'elasticOut',
                animationDelay: function (idx) {
                    return Math.random() * 200;
                }
            }
        ]
    };

    // 모바일 화면에서 차트 옵션 변경
    if (window.innerWidth <= 480) {
        option.legend.bottom = '0';
        option.legend.itemGap = 5;
        option.legend.pageIconSize = [6, 6];
        option.legend.textStyle.fontSize = 8;
        option.series[0].center = ['50%', '40%'];
        option.series[0].radius = ['30%', '55%'];
    } else if (window.innerWidth <= 768) {
        option.legend.bottom = '2%';
        option.legend.itemGap = 8;
        option.legend.pageIconSize = [8, 8];
        option.legend.textStyle.fontSize = 10;
        option.series[0].center = ['50%', '45%'];
        option.series[0].radius = ['35%', '60%'];
    }

    myChart.setOption(option);

    // 반응형 처리
    window.addEventListener('resize', function() {
        myChart.resize();
        
        // 창 크기에 따라 차트 옵션 다시 설정
        const newOption = {...option};
        
        if (window.innerWidth <= 480) {
            newOption.legend.bottom = '0';
            newOption.legend.itemGap = 5;
            newOption.legend.pageIconSize = [6, 6];
            newOption.legend.textStyle.fontSize = 8;
            newOption.series[0].center = ['50%', '40%'];
            newOption.series[0].radius = ['30%', '55%'];
        } else if (window.innerWidth <= 768) {
            newOption.legend.bottom = '2%';
            newOption.legend.itemGap = 8;
            newOption.legend.pageIconSize = [8, 8];
            newOption.legend.textStyle.fontSize = 10;
            newOption.series[0].center = ['50%', '45%'];
            newOption.series[0].radius = ['35%', '60%'];
        } else {
            newOption.legend.bottom = '5%';
            newOption.legend.itemGap = 12;
            newOption.legend.pageIconSize = [12, 12];
            newOption.legend.textStyle.fontSize = 12;
            newOption.series[0].center = ['50%', '50%'];
            newOption.series[0].radius = ['40%', '70%'];
        }
        
        myChart.setOption(newOption);
    });
}

// 탭 시스템 초기화
function initTabSystem() {
    // 탭 요소들 가져오기
    const allTab = document.getElementById('tab-all');
    const buyTab = document.getElementById('tab-buy');
    const sellTab = document.getElementById('tab-sell');
    
    // 주식 카드 요소들 가져오기
    const stockCards = document.querySelectorAll('.stock-card');
    
    // 탭 활성화 및 필터링 함수
    function activateTabAndFilter(tab, filterType) {
        // 모든 탭 비활성화
        [allTab, buyTab, sellTab].forEach(t => {
            if (t) {
                t.classList.remove('active', 'border-point', 'text-cdark');
                t.classList.add('text-gray-600');
            }
        });
        
        // 선택된 탭 활성화
        if (tab) {
            tab.classList.remove('text-gray-600');
            tab.classList.add('active', 'text-cdark');
        }
        
        // 주식 카드 필터링
        let visibleCount = 0;
        
        stockCards.forEach(card => {
            const cardType = card.getAttribute('data-type');
            
            if (filterType === 'all') {
                // 전체 탭에서는 모든 카드 표시
                card.style.display = '';
                visibleCount++;
            } else if (cardType === filterType) {
                // 해당 필터 타입과 일치하는 카드만 표시
                card.style.display = '';
                visibleCount++;
            } else {
                // 그 외에는 숨김
                card.style.display = 'none';
            }
        });
    }
    
    // 초기 탭 설정 (전체 탭 활성화)
    activateTabAndFilter(allTab, 'all');
    
    // 탭 클릭 이벤트 리스너 추가
    if (allTab) {
        allTab.addEventListener('click', function(e) {
            e.preventDefault();
            activateTabAndFilter(allTab, 'all');
        });
    }
    
    if (buyTab) {
        buyTab.addEventListener('click', function(e) {
            e.preventDefault();
            activateTabAndFilter(buyTab, 'buy');
        });
    }
    
    if (sellTab) {
        sellTab.addEventListener('click', function(e) {
            e.preventDefault();
            activateTabAndFilter(sellTab, 'sell');
        });
    }
}
</script>
@endpush
@endsection 