@extends('layouts.app')

@section('content')
<style>
    /* Keyframes from index-new */
    @keyframes cardFloat1 { 0%, 100% { transform: translateY(0) rotate(-3deg); } 50% { transform: translateY(-12px) rotate(-1deg); } }
    @keyframes cardFloat2 { 0%, 100% { transform: translateY(0) rotate(2deg); } 50% { transform: translateY(-10px) rotate(4deg); } }
    @keyframes cardFloat3 { 0%, 100% { transform: translateY(0) rotate(-1deg); } 50% { transform: translateY(-8px) rotate(1deg); } }

    .animate-slideUp-1 { animation: slideUp 0.8s ease 0.1s forwards; }
    .animate-slideUp-2 { animation: slideUp 0.8s ease 0.2s forwards; }
    .animate-slideUp-3 { animation: slideUp 0.8s ease 0.3s forwards; }
    .animate-slideUp-4 { animation: slideUp 0.8s ease 0.4s forwards; }
    .animate-cardFloat1 { animation: cardFloat1 4s ease-in-out infinite; }
    .animate-cardFloat2 { animation: cardFloat2 4.5s ease-in-out infinite; }
    .animate-cardFloat3 { animation: cardFloat3 5s ease-in-out infinite; }

    /* 카드 hover 효과 - index-new 스타일 */
    .card-lift {
        transition: all 400ms;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .card-lift:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        border-color: #FF4D4D;
    }

    /* 슬라이더 스타일 */
    .invest-slider {
        -webkit-appearance: none; appearance: none;
        width: 100%; height: 8px;
        background: rgba(255,255,255,0.1);
        border-radius: 999px;
        cursor: pointer;
    }
    .invest-slider::-webkit-slider-thumb {
        -webkit-appearance: none; appearance: none;
        width: 20px; height: 20px;
        border-radius: 50%;
        background: #FF4D4D;
        box-shadow: 0 0 10px rgba(255,77,77,0.5);
        cursor: pointer;
    }

    /* 스크롤 트리거 페이드인 */
    .scroll-reveal { opacity: 0; transform: translateY(30px); transition: all 0.8s ease; }
    .scroll-reveal.visible { opacity: 1; transform: translateY(0); }
</style>

<div class="main-page">

    <!-- ===== Hero Section — index-new 디자인 재현 ===== -->
    <section class="flex items-center pt-[20px] pb-10 px-4 md:min-h-[85vh] md:pt-[40px] md:pb-20 md:px-8 bg-gradient-to-br from-[#3D4148] to-[#2D3047] relative overflow-hidden">
        <div class="absolute -top-[20%] -right-[10%] w-[700px] h-[700px] rounded-full bg-[radial-gradient(circle,rgba(255,77,77,0.08)_0%,transparent_60%)]"></div>
        <div class="absolute -bottom-[30%] -left-[15%] w-[600px] h-[600px] rounded-full bg-[radial-gradient(circle,rgba(78,205,196,0.06)_0%,transparent_60%)]"></div>

        <div class="max-w-[1400px] w-full mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-12 lg:gap-24 items-center relative z-[1] text-center lg:text-left">
            <div class="lg:max-w-[580px]">
                <div class="inline-flex items-center gap-2 bg-white/10 text-white/90 py-1.5 px-3 md:py-2 md:px-4 rounded-full text-[0.75rem] md:text-[0.85rem] font-medium mb-4 md:mb-6 border border-white/[0.15] animate-fadeIn">🌱 자녀와 함께 성장하는 경제 커뮤니티</div>
                <h1 class="font-outfit text-[clamp(2.5rem,5vw,3.75rem)] font-extrabold leading-[1.2] text-white mb-6 tracking-[-0.02em] opacity-0 animate-slideUp-1">
                    우리 아이의<br>
                    <span class="text-new-primary">첫 경제교육</span>을<br>
                    시작하세요
                </h1>
                <p class="text-sm md:text-[1.2rem] text-white/75 mb-6 md:mb-10 leading-[1.7] md:leading-[1.8] opacity-0 animate-slideUp-2">
                    용돈 관리부터 투자 기초까지.<br>
                    게임처럼 재미있게, 습관처럼 자연스럽게 배워요.
                </p>
                <div class="flex flex-wrap gap-2 md:gap-3 mb-6 md:mb-10 justify-center lg:justify-start opacity-0 animate-slideUp-3">
                    <div class="flex items-center gap-1.5 md:gap-2 bg-white/[0.08] backdrop-blur-[10px] py-1.5 px-3 md:py-[0.6rem] md:px-4 rounded-lg text-xs md:text-[0.875rem] font-medium text-white/90 border border-white/10 transition-all duration-300 hover:bg-white/[0.12] hover:-translate-y-0.5">
                        <span>📚</span><span>단계별 커리큘럼</span>
                    </div>
                    <div class="flex items-center gap-1.5 md:gap-2 bg-white/[0.08] backdrop-blur-[10px] py-1.5 px-3 md:py-[0.6rem] md:px-4 rounded-lg text-xs md:text-[0.875rem] font-medium text-white/90 border border-white/10 transition-all duration-300 hover:bg-white/[0.12] hover:-translate-y-0.5">
                        <span>🎮</span><span>경제 게임</span>
                    </div>
                    <div class="flex items-center gap-1.5 md:gap-2 bg-white/[0.08] backdrop-blur-[10px] py-1.5 px-3 md:py-[0.6rem] md:px-4 rounded-lg text-xs md:text-[0.875rem] font-medium text-white/90 border border-white/10 transition-all duration-300 hover:bg-white/[0.12] hover:-translate-y-0.5">
                        <span>📰</span><span>어린이 뉴스</span>
                    </div>
                </div>
                <div class="flex gap-3 md:gap-4 justify-center lg:justify-start opacity-0 animate-slideUp-4">
                    <a href="{{ route('course.l1.intro') }}" class="bg-gradient-to-br from-new-primary to-new-coral text-white py-3 px-5 md:py-4 md:px-8 rounded-[10px] font-bold text-sm md:text-base inline-flex items-center gap-2 transition-all duration-300 shadow-[0_8px_25px_rgba(255,77,77,0.35)] hover:-translate-y-[3px] hover:shadow-[0_12px_35px_rgba(255,77,77,0.45)]">코스 확인하기</a>
                    <a href="{{ route('introduce') }}" class="text-white py-3 px-5 md:py-4 md:px-7 rounded-[10px] font-semibold text-sm md:text-base border-2 border-white/25 transition-all duration-300 hover:border-white/50 hover:bg-white/5">자세히 보기</a>
                </div>
            </div>
            <div class="relative hidden lg:flex justify-center items-center">
                <div class="relative w-full max-w-[480px] aspect-square">
                    <div class="absolute top-[5%] left-0 w-[260px] z-[3] bg-white rounded-[20px] p-7 shadow-[0_25px_50px_rgba(0,0,0,0.25)] animate-cardFloat1">
                        <div class="w-[52px] h-[52px] rounded-[14px] flex items-center justify-center text-2xl mb-3.5 bg-gradient-to-br from-[#FFE66D] to-[#FFD93D]">💰</div>
                        <div class="font-bold text-base text-new-secondary mb-1">용돈 관리</div>
                        <div class="text-[0.85rem] text-gray-500 leading-[1.5]">수입과 지출을 기록하며 돈의 흐름을 이해해요</div>
                    </div>
                    <div class="absolute top-[35%] right-0 w-[240px] z-[2] bg-white rounded-[20px] p-7 shadow-[0_25px_50px_rgba(0,0,0,0.25)] animate-cardFloat2">
                        <div class="w-[52px] h-[52px] rounded-[14px] flex items-center justify-center text-2xl mb-3.5 bg-gradient-to-br from-new-mint to-[#26D0CE]">📈</div>
                        <div class="font-bold text-base text-new-secondary mb-1">투자의 기초</div>
                        <div class="text-[0.85rem] text-gray-500 leading-[1.5]">주식과 펀드가 무엇인지 쉽게 배워요</div>
                    </div>
                    <div class="absolute bottom-[10%] left-[15%] w-[200px] z-[1] bg-white rounded-[20px] p-7 shadow-[0_25px_50px_rgba(0,0,0,0.25)] animate-cardFloat3">
                        <div class="w-[52px] h-[52px] rounded-[14px] flex items-center justify-center text-2xl mb-3.5 bg-gradient-to-br from-new-coral to-new-primary">🎯</div>
                        <div class="font-bold text-base text-new-secondary mb-1">목표 저축</div>
                        <div class="text-[0.85rem] text-gray-500 leading-[1.5]">원하는 것을 위해 저축하는 습관을 길러요</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Features Section ===== -->
    <section class="py-10 px-4 md:py-24 md:px-8 bg-white scroll-reveal">
        <div class="text-center max-w-[650px] mx-auto mb-8 md:mb-16">
            <span class="inline-block bg-new-secondary text-white py-1.5 px-4 rounded-md text-xs font-semibold tracking-[0.08em] uppercase mb-4">Why MQWAY?</span>
            <h2 class="font-outfit text-[clamp(1.875rem,4vw,2.5rem)] font-extrabold text-new-secondary mb-4 tracking-[-0.02em]">MQ는 이런 커뮤니티입니다</h2>
            <p class="text-[1.05rem] text-gray-500">아이들과 부모가 함께, 돈을 통해 스스로를 이해하고 성장하는 여정</p>
        </div>
        <div class="max-w-[1200px] mx-auto grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6">
            @foreach($features as $feature)
            <div class="bg-new-surface rounded-xl md:rounded-2xl p-4 md:p-8 overflow-hidden transition-all duration-[400ms] border border-transparent hover:-translate-y-1.5 hover:shadow-[0_15px_40px_rgba(0,0,0,0.08)] hover:border-new-primary">
                <div class="w-10 h-10 md:w-[60px] md:h-[60px] rounded-xl md:rounded-[14px] flex items-center justify-center text-xl md:text-[1.75rem] mb-3 md:mb-5 bg-white shadow-[0_4px_12px_rgba(0,0,0,0.06)]">{{ $feature['emoji'] }}</div>
                <h3 class="text-sm md:text-[1.2rem] font-bold text-new-secondary mb-1 md:mb-2">{{ $feature['title'] }}</h3>
                <p class="text-xs md:text-[0.9rem] text-gray-500 leading-[1.5] md:leading-[1.7] hidden md:block">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <!-- ===== 추천 콘텐츠 Section ===== -->
    @if($recommendedContents->count() > 0)
    <section class="py-10 px-4 md:py-24 md:px-8 bg-new-surface scroll-reveal">
        <div class="max-w-[1200px] mx-auto">
            <div class="flex justify-between items-center mb-6 md:mb-10">
                <div>
                    <span class="inline-block bg-new-secondary text-white py-1 px-3 rounded-md text-[0.65rem] md:text-xs font-semibold tracking-[0.08em] uppercase mb-2">Contents</span>
                    <h2 class="font-outfit text-xl md:text-[2rem] font-extrabold text-new-secondary tracking-[-0.02em]">추천 콘텐츠</h2>
                </div>
                <a href="{{ route('board-content.index') }}" class="text-sm md:text-base text-gray-500 hover:text-new-primary transition-colors flex items-center gap-1 font-medium">
                    더보기
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="flex gap-3 overflow-x-auto pb-4 snap-x snap-mandatory md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-5 md:overflow-visible md:pb-0 md:snap-none scrollbar-hide">
                @foreach($recommendedContents->take(8) as $post)
                <a href="{{ route('board-content.show', $post->idx) }}" class="bg-white rounded-2xl overflow-hidden card-lift block min-w-[70vw] snap-start md:min-w-0">
                    <div class="aspect-[16/10] bg-gradient-to-br from-new-secondary/30 to-new-secondary relative flex items-center justify-center overflow-hidden">
                        @if($post->mq_image)
                            <img src="{{ $post->mq_image }}" alt="{{ $post->mq_title }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-[2.5rem] opacity-50">📰</span>
                        @endif
                        @if($post->mq_category)
                            <span class="absolute top-3 left-3 {{ $boardContentColors[$post->mq_category] ?? 'bg-new-primary text-white' }} py-[0.2rem] px-[0.6rem] rounded text-[0.7rem] font-semibold">{{ $post->mq_category }}</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-[0.95rem] font-semibold text-new-secondary mb-1.5 line-clamp-2 leading-[1.4]">{{ $post->mq_title }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ===== 인사이트 만화 Section ===== -->
    @if($cartoonContents->count() > 0)
    <section class="py-10 px-4 md:py-24 md:px-8 bg-white scroll-reveal">
        <div class="max-w-[1200px] mx-auto">
            <div class="flex justify-between items-center mb-6 md:mb-10">
                <div>
                    <span class="inline-block bg-new-coral/10 text-new-coral py-1 px-3 rounded-md text-[0.65rem] md:text-xs font-semibold tracking-[0.08em] uppercase mb-2">Comics</span>
                    <h2 class="font-outfit text-xl md:text-[2rem] font-extrabold text-new-secondary tracking-[-0.02em]">인사이트 만화</h2>
                </div>
                <a href="{{ route('board-cartoon.index') }}" class="text-sm md:text-base text-gray-500 hover:text-new-primary transition-colors flex items-center gap-1 font-medium">
                    더보기
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="flex gap-3 overflow-x-auto pb-4 snap-x snap-mandatory md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-5 md:overflow-visible md:pb-0 md:snap-none scrollbar-hide">
                @php $cartoonGradients = ['from-[#FFF3E0] to-[#FFE0B2]', 'from-[#E8F5E9] to-[#C8E6C9]', 'from-[#E3F2FD] to-[#BBDEFB]', 'from-[#FCE4EC] to-[#F8BBD0]']; @endphp
                @foreach($cartoonContents->take(8) as $index => $post)
                <a href="{{ route('board-cartoon.show', $post->idx) }}" class="bg-new-surface rounded-2xl overflow-hidden card-lift block min-w-[70vw] snap-start md:min-w-0">
                    <div class="aspect-[16/10] bg-gradient-to-br {{ $cartoonGradients[$index % 4] }} relative flex items-center justify-center overflow-hidden">
                        @if($post->mq_image)
                            <img src="{{ $post->mq_image }}" alt="{{ $post->mq_title }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-[3rem] opacity-60">🎨</span>
                        @endif
                        @if($post->mq_category)
                            <span class="absolute top-3 left-3 {{ $boardCartoonColors[$post->mq_category] ?? 'bg-new-coral text-white' }} py-[0.2rem] px-[0.6rem] rounded text-[0.7rem] font-semibold">{{ $post->mq_category }}</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-[0.95rem] font-semibold text-new-secondary mb-1.5 line-clamp-2 leading-[1.4]">{{ $post->mq_title }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ===== 주요 뉴스 Section ===== -->
    @if($latestNews->count() > 0)
    <section class="py-10 px-4 md:py-24 md:px-8 bg-white scroll-reveal">
        <div class="max-w-[1200px] mx-auto">
            <div class="flex justify-between items-center mb-6 md:mb-10">
                <div>
                    <span class="inline-block bg-indigo-500 text-white py-1 px-3 rounded-md text-[0.65rem] md:text-xs font-semibold tracking-[0.08em] uppercase mb-2">News</span>
                    <h2 class="font-outfit text-xl md:text-[2rem] font-extrabold text-new-secondary tracking-[-0.02em]">주요 뉴스</h2>
                </div>
                <a href="{{ route('board-news.index') }}" class="text-sm md:text-base text-gray-500 hover:text-new-primary transition-colors flex items-center gap-1 font-medium">
                    더보기
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="grid md:grid-cols-2 gap-4 md:gap-5">
                @foreach($latestNews as $news)
                <a href="{{ $news->mq_source_url }}" target="_blank" rel="noopener noreferrer" class="flex gap-4 bg-new-surface rounded-2xl p-4 md:p-5 card-lift group">
                    @if($news->mq_image_url)
                    <div class="w-24 h-24 md:w-28 md:h-28 flex-shrink-0 rounded-xl overflow-hidden bg-gray-100">
                        <img src="{{ $news->mq_image_url }}" alt="{{ $news->mq_title }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        @if($news->mq_category)
                            <span class="inline-block text-[0.7rem] font-semibold py-[2px] px-2 rounded {{ $newsCategoryColors[$news->mq_category] ?? 'bg-gray-100 text-gray-600' }}">{{ $news->mq_category }}</span>
                        @endif
                        <h3 class="text-[0.95rem] font-semibold text-new-secondary mb-1.5 line-clamp-2 leading-[1.4] mt-1.5">{{ $news->mq_title }}</h3>
                        <p class="text-xs text-gray-400 line-clamp-2">{{ $news->mq_summary ?? '' }}</p>
                        <p class="text-xs text-gray-300 mt-1.5">{{ $news->mq_company ?? '' }} · {{ \Carbon\Carbon::parse($news->mq_published_date)->format('Y.m.d') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ===== 투자 리서치 Section ===== -->
    <section class="py-10 px-4 md:py-24 md:px-8 bg-new-surface scroll-reveal">
        <div class="max-w-[1200px] mx-auto">
            <div class="flex justify-between items-center mb-6 md:mb-10">
                <div>
                    <span class="inline-block bg-new-mint/10 text-new-mint py-1 px-3 rounded-md text-[0.65rem] md:text-xs font-semibold tracking-[0.08em] uppercase mb-2">Research</span>
                    <h2 class="font-outfit text-xl md:text-[2rem] font-extrabold text-new-secondary tracking-[-0.02em]">투자 리서치</h2>
                </div>
                @if($isLoggedIn)
                <a href="{{ route('board-research.index') }}" class="text-sm md:text-base text-gray-500 hover:text-new-primary transition-colors flex items-center gap-1 font-medium">
                    더보기
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
                @endif
            </div>

            @if($isLoggedIn && $researchContents->count() > 0)
                <div class="flex gap-3 overflow-x-auto pb-4 snap-x snap-mandatory md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-5 md:overflow-visible md:pb-0 md:snap-none scrollbar-hide">
                    @foreach($researchContents->take(8) as $post)
                    <a href="{{ route('board-research.show', $post->idx) }}" class="bg-white rounded-2xl overflow-hidden card-lift block min-w-[70vw] snap-start md:min-w-0">
                        <div class="aspect-[16/10] bg-gradient-to-br from-new-mint/20 to-new-mint/5 relative flex items-center justify-center overflow-hidden">
                            @if($post->mq_image)
                                <img src="{{ $post->mq_image }}" alt="{{ $post->mq_title }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-[2.5rem] opacity-50">📊</span>
                            @endif
                            @if($post->mq_category)
                                <span class="absolute top-3 left-3 {{ $boardResearchColors[$post->mq_category] ?? 'bg-new-mint text-white' }} py-[0.2rem] px-[0.6rem] rounded text-[0.7rem] font-semibold">{{ $post->mq_category }}</span>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-[0.95rem] font-semibold text-new-secondary mb-1.5 line-clamp-2 leading-[1.4]">{{ $post->mq_title }}</h3>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <!-- 비회원: 블러 + 로그인 유도 -->
                <div class="relative">
                    <div class="flex gap-3 overflow-x-auto pb-4 snap-x snap-mandatory md:grid md:grid-cols-2 lg:grid-cols-4 md:gap-5 md:overflow-visible md:pb-0 md:snap-none scrollbar-hide blur-[6px] select-none pointer-events-none">
                        @for($i = 0; $i < 4; $i++)
                        <div class="bg-white rounded-2xl overflow-hidden border border-black/5 block min-w-[70vw] snap-start md:min-w-0">
                            <div class="aspect-[16/10] bg-gradient-to-br from-new-mint/20 to-new-mint/5 relative flex items-center justify-center">
                                <span class="text-[2.5rem] opacity-50">{{ ['📊','📈','💰','🏦'][$i] }}</span>
                                <span class="absolute top-3 left-3 bg-new-mint text-white py-[0.2rem] px-[0.6rem] rounded text-[0.7rem] font-semibold">{{ ['리서치','분석','포트폴리오','경제지표'][$i] }}</span>
                            </div>
                            <div class="p-4">
                                <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                                <div class="h-3 bg-gray-100 rounded w-2/3"></div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,0.12)] p-6 md:p-10 text-center max-w-md mx-4">
                            <div class="w-14 h-14 bg-new-mint/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7 text-new-mint" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h3 class="text-lg md:text-xl font-bold text-new-secondary mb-2">전문 투자 리서치</h3>
                            <p class="text-sm text-gray-500 mb-5">300개 이상의 투자 리서치 콘텐츠를<br>로그인 후 무료로 이용하세요.</p>
                            <a href="{{ url('/login') }}" class="inline-block bg-gradient-to-br from-new-mint to-[#3BB5AD] text-white py-3 px-8 rounded-xl font-semibold text-sm transition-all duration-300 shadow-[0_4px_15px_rgba(78,205,196,0.3)] hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(78,205,196,0.4)]">
                                로그인하고 리서치 보기
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- ===== 투자 시뮬레이션 차트 Section ===== -->
    <section class="py-12 px-4 md:py-24 md:px-8 bg-gradient-to-b from-[#2D3047] to-new-secondary relative overflow-hidden scroll-reveal">
        <div class="absolute top-0 left-0 w-full h-full opacity-[0.03]" style="background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2260%22 height=%2260%22><line x1=%220%22 y1=%2260%22 x2=%2260%22 y2=%220%22 stroke=%22white%22 stroke-width=%220.5%22/></svg>');"></div>

        <div class="max-w-[1000px] mx-auto relative z-[1]">
            <div class="text-center mb-8 md:mb-12">
                <span class="inline-block bg-new-primary/20 text-new-primary text-xs md:text-sm font-semibold py-1.5 px-4 rounded-full mb-4">📊 투자 시뮬레이션</span>
                <h2 class="text-xl md:text-[2.2rem] font-bold text-white leading-tight mb-3">
                    매달 <span class="text-new-primary" id="chartTitleAmount">1만원</span>씩 투자한다면?
                </h2>
                <p class="text-sm md:text-base text-white/60">최근 5년간(2019~2024) 애플 · 나스닥 · 예금 실제 투자 수익을 비교해보세요</p>
            </div>

            <div class="bg-white/[0.06] backdrop-blur-sm border border-white/10 rounded-2xl p-5 md:p-8">
                <!-- 슬라이더 -->
                <div class="flex flex-col md:flex-row items-center gap-4 md:gap-8 mb-6">
                    <label class="text-white/80 text-sm font-medium whitespace-nowrap">월 투자금액</label>
                    <div class="flex-1 w-full">
                        <input type="range" id="monthlyInvestment" min="1" max="100" step="1" value="1" class="invest-slider w-full">
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="investmentDisplay" class="font-outfit text-2xl md:text-3xl font-extrabold text-new-primary">1</span>
                        <span class="text-white/60 text-sm">만원</span>
                    </div>
                </div>

                <!-- 요약 카드 -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="bg-white/[0.06] rounded-xl p-3 md:p-4 text-center">
                        <p class="text-white/50 text-[0.65rem] md:text-xs mb-1">총 투자 원금</p>
                        <p id="totalInvestment" class="text-white font-bold text-sm md:text-lg font-outfit">-</p>
                    </div>
                    <div class="bg-white/[0.06] rounded-xl p-3 md:p-4 text-center">
                        <p class="text-white/50 text-[0.65rem] md:text-xs mb-1">애플 (AAPL)</p>
                        <p id="sp500Result" class="text-new-primary font-bold text-sm md:text-lg font-outfit">-</p>
                    </div>
                    <div class="bg-white/[0.06] rounded-xl p-3 md:p-4 text-center">
                        <p class="text-white/50 text-[0.65rem] md:text-xs mb-1">나스닥 (QQQ)</p>
                        <p id="kospiResult" class="text-new-mint font-bold text-sm md:text-lg font-outfit">-</p>
                    </div>
                </div>

                <!-- 차트 -->
                <div id="investmentChart" class="w-full rounded-xl" style="height: 350px;"></div>

                <!-- 범례 -->
                <div class="flex flex-wrap justify-center gap-4 md:gap-8 mt-5">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-new-primary"></span><span class="text-white/60 text-xs md:text-sm">애플 (AAPL)</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-new-mint"></span><span class="text-white/60 text-xs md:text-sm">나스닥 (QQQ)</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-new-amber"></span><span class="text-white/60 text-xs md:text-sm">은행 예금</span></div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-white/30"></span><span class="text-white/60 text-xs md:text-sm">투자 원금</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Stats Section ===== -->
    <section class="py-10 px-4 md:py-20 md:px-8 bg-new-secondary scroll-reveal">
        <div class="max-w-[1000px] mx-auto grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8 text-center">
            <div class="text-white">
                <div class="font-outfit text-2xl md:text-5xl font-extrabold text-new-primary mb-1">300+</div>
                <div class="text-xs md:text-[0.95rem] opacity-75">투자 리서치</div>
            </div>
            <div class="text-white">
                <div class="font-outfit text-2xl md:text-5xl font-extrabold text-new-primary mb-1">200+</div>
                <div class="text-xs md:text-[0.95rem] opacity-75">어린이 콘텐츠</div>
            </div>
            <div class="text-white">
                <div class="font-outfit text-2xl md:text-5xl font-extrabold text-new-primary mb-1">100+</div>
                <div class="text-xs md:text-[0.95rem] opacity-75">인사이트 만화</div>
            </div>
            <div class="text-white">
                <div class="font-outfit text-2xl md:text-5xl font-extrabold text-new-mint mb-1">1개월</div>
                <div class="text-xs md:text-[0.95rem] opacity-75">단기 코스</div>
            </div>
        </div>
    </section>


    <!-- ===== CTA Section ===== -->
    <section class="py-10 px-4 md:py-24 md:px-8 bg-new-surface text-center scroll-reveal">
        <div class="max-w-[750px] mx-auto bg-new-secondary rounded-3xl py-10 px-6 md:py-16 md:px-12 relative overflow-hidden">
            <div class="absolute -top-[40%] -right-[15%] w-[350px] h-[350px] bg-new-primary/10 rounded-full"></div>
            <h2 class="font-outfit text-[clamp(1.5rem,3vw,2.25rem)] font-extrabold text-white mb-3 relative z-[1]">지금 심어주는 경제 습관,<br>아이의 세상을 넓혀줍니다</h2>
            <p class="text-base text-white/75 mb-8 relative z-[1]">MQ와 함께 우리 아이의 첫 금융 교육을 시작해보세요.</p>
            <a href="{{ route('course.l1.intro') }}" class="bg-gradient-to-br from-new-primary to-new-coral text-white py-4 px-9 rounded-[10px] font-bold text-base inline-flex items-center gap-2 transition-all duration-300 relative z-[1] shadow-[0_8px_25px_rgba(255,77,77,0.35)] hover:-translate-y-[3px] hover:shadow-[0_12px_35px_rgba(255,77,77,0.45)]">코스 확인하기</a>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ===== 스크롤 애니메이션 =====
    const revealEls = document.querySelectorAll('.scroll-reveal');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    revealEls.forEach(el => revealObserver.observe(el));

    // ===== 투자 시뮬레이션 차트 (월별 변동률 기반) =====
    const slider = document.getElementById('monthlyInvestment');
    const display = document.getElementById('investmentDisplay');
    const titleAmount = document.getElementById('chartTitleAmount');
    const totalEl = document.getElementById('totalInvestment');
    const aaplEl = document.getElementById('sp500Result');
    const qqqEl = document.getElementById('kospiResult');
    const chartDom = document.getElementById('investmentChart');
    if (!chartDom || !slider) return;

    const myChart = echarts.init(chartDom);
    let rawData = null;

    // 숫자 포맷 (원 단위 -> 만원/억원 표시)
    function formatWon(value) {
        if (value >= 100000000) {
            const uk = Math.floor(value / 100000000);
            const remain = Math.floor((value % 100000000) / 10000);
            return remain === 0 ? uk + '억원' : uk + '억 ' + remain.toLocaleString() + '만원';
        }
        if (value >= 10000) {
            return Math.floor(value / 10000).toLocaleString() + '만원';
        }
        return value.toLocaleString() + '원';
    }

    // 기존 프로젝트의 누적 계산 함수 (월별 변동률 기반)
    function calculateCumulativeValues(data, monthlyInvestment) {
        let results = [];
        let cumulativeAAPL = 0;
        let cumulativeQQQ = 0;
        let cumulativeBank = 0;

        for (let i = 0; i < data.length; i++) {
            const monthData = data[i];

            // 매달: 기존 잔고 × (1 + 변동률) + 신규 투자금
            cumulativeAAPL = cumulativeAAPL * (1 + monthData.AAPL) + monthlyInvestment;
            cumulativeQQQ = cumulativeQQQ * (1 + monthData.QQQ) + monthlyInvestment;
            cumulativeBank = cumulativeBank * (1 + monthData.bank) + monthlyInvestment;

            results.push({
                date: monthData.date,
                AAPL: Math.round(cumulativeAAPL),
                QQQ: Math.round(cumulativeQQQ),
                bank: Math.round(cumulativeBank)
            });
        }

        return results;
    }

    function loadChart() {
        if (!rawData) return;

        const inputValue = parseInt(slider.value) || 1;
        const monthlyInvestment = inputValue * 10000; // 만원 → 원 단위
        const totalMonths = rawData.length;
        const totalPrincipal = monthlyInvestment * totalMonths;

        // 누적 계산
        const processed = calculateCumulativeValues(rawData, monthlyInvestment);

        const labels = processed.map(d => d.date);
        const aaplData = processed.map(d => d.AAPL);
        const qqqData = processed.map(d => d.QQQ);
        const bankData = processed.map(d => d.bank);
        const principalData = processed.map((_, i) => monthlyInvestment * (i + 1));

        // 요약 카드 업데이트
        totalEl.textContent = formatWon(totalPrincipal);
        aaplEl.textContent = formatWon(aaplData[aaplData.length - 1]);
        qqqEl.textContent = formatWon(qqqData[qqqData.length - 1]);

        myChart.setOption({
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(45,48,71,0.95)',
                borderColor: 'rgba(255,255,255,0.1)',
                textStyle: { color: '#fff', fontSize: 12 },
                formatter: function(p) {
                    let r = '<div style="font-weight:600;margin-bottom:6px">' + p[0].axisValue + '</div>';
                    p.forEach(s => {
                        r += '<div style="display:flex;align-items:center;gap:6px;margin:3px 0">'
                            + '<span style="width:8px;height:8px;border-radius:50%;background:' + s.color + ';display:inline-block"></span>'
                            + s.seriesName + ': <strong>' + formatWon(s.value) + '</strong></div>';
                    });
                    return r;
                }
            },
            grid: { left: '3%', right: '4%', bottom: '5%', top: '5%', containLabel: true },
            xAxis: {
                type: 'category',
                data: labels,
                axisLine: { lineStyle: { color: 'rgba(255,255,255,0.1)' } },
                axisLabel: {
                    color: 'rgba(255,255,255,0.5)',
                    fontSize: 11,
                    interval: function(index) {
                        return labels[index] && labels[index].endsWith('-01');
                    },
                    formatter: function(v) { return v.substring(0, 4); }
                },
                axisTick: { show: false }
            },
            yAxis: {
                type: 'value',
                axisLine: { show: false },
                splitLine: { lineStyle: { color: 'rgba(255,255,255,0.05)' } },
                axisLabel: {
                    color: 'rgba(255,255,255,0.5)',
                    fontSize: 11,
                    formatter: function(v) {
                        if (v >= 100000000) return (v / 100000000) + '억';
                        if (v >= 10000000) return Math.floor(v / 10000000) + '천만';
                        if (v >= 10000) return Math.floor(v / 10000) + '만';
                        return v.toLocaleString();
                    }
                }
            },
            series: [
                {
                    name: '투자 원금',
                    type: 'line',
                    data: principalData,
                    lineStyle: { color: 'rgba(255,255,255,0.3)', width: 2, type: 'dashed' },
                    itemStyle: { color: 'rgba(255,255,255,0.3)' },
                    symbol: 'none',
                    areaStyle: { color: 'rgba(255,255,255,0.03)' }
                },
                {
                    name: '애플 (AAPL)',
                    type: 'line',
                    data: aaplData,
                    lineStyle: { color: '#FF4D4D', width: 3 },
                    itemStyle: { color: '#FF4D4D' },
                    symbol: 'none',
                    smooth: true,
                    areaStyle: { color: new echarts.graphic.LinearGradient(0,0,0,1,[{offset:0,color:'rgba(255,77,77,0.2)'},{offset:1,color:'rgba(255,77,77,0)'}]) }
                },
                {
                    name: '나스닥 (QQQ)',
                    type: 'line',
                    data: qqqData,
                    lineStyle: { color: '#4ECDC4', width: 3 },
                    itemStyle: { color: '#4ECDC4' },
                    symbol: 'none',
                    smooth: true,
                    areaStyle: { color: new echarts.graphic.LinearGradient(0,0,0,1,[{offset:0,color:'rgba(78,205,196,0.2)'},{offset:1,color:'rgba(78,205,196,0)'}]) }
                },
                {
                    name: '은행 예금',
                    type: 'line',
                    data: bankData,
                    lineStyle: { color: '#FFB347', width: 2 },
                    itemStyle: { color: '#FFB347' },
                    symbol: 'none',
                    smooth: true,
                    areaStyle: { color: new echarts.graphic.LinearGradient(0,0,0,1,[{offset:0,color:'rgba(255,179,71,0.1)'},{offset:1,color:'rgba(255,179,71,0)'}]) }
                }
            ]
        });
    }

    // JSON 데이터 로드 (월별 변동률 파일)
    fetch('{{ asset("storage/etc/2019_2024_market_month_change_rate.json") }}')
        .then(res => {
            if (!res.ok) throw new Error('데이터 로드 실패');
            return res.json();
        })
        .then(data => {
            // 날짜순 정렬
            data.sort((a, b) => new Date(a.date) - new Date(b.date));
            rawData = data;
            loadChart();
        })
        .catch(err => console.error('시장 데이터 로드 실패:', err));

    slider.addEventListener('input', function() {
        display.textContent = this.value;
        titleAmount.textContent = this.value + '만원';
        loadChart();
    });

    window.addEventListener('resize', () => myChart.resize());
});
</script>
@endpush