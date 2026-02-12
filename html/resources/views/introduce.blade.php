@extends('layouts.app')

@section('content')
<style>
    /* Gradient Text */
    .text-gradient {
        background: linear-gradient(135deg, #FF4D4D 0%, #FF6B6B 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Feature Card Styles */
    .feature-card {
        background: #fff;
        border-radius: 1.5rem;
        padding: 2rem;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        height: 100%;
    }
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        border-color: rgba(78, 205, 196, 0.3);
    }
    
    /* Custom List Style */
    .custom-list li {
        position: relative;
        padding-left: 1.5rem;
        margin-bottom: 0.5rem;
    }
    .custom-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0.6rem;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #4ECDC4;
    }
</style>

<div class="main-page bg-[#F8F9FB] min-h-screen">
    
    <!-- ===== Hero Section ===== -->
    <section class="relative bg-gradient-to-br from-[#3D4148] to-[#2D3047] pt-20 pb-32 px-4 overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-[radial-gradient(circle,rgba(78,205,196,0.1)_0%,transparent_70%)] blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-[radial-gradient(circle,rgba(255,77,77,0.08)_0%,transparent_70%)] blur-3xl pointer-events-none"></div>

        <div class="container mx-auto max-w-4xl relative z-10 text-center text-white">
            <div class="inline-flex items-center gap-2 bg-white/10 text-white/90 py-1.5 px-4 rounded-full text-sm font-medium mb-6 border border-white/10 backdrop-blur-md animate-fadeIn">
                <span>✨</span> <span>About MQ</span>
            </div>
            <h1 class="font-outfit text-4xl md:text-5xl lg:text-7xl font-bold mb-6 leading-tight animate-slideUp" style="animation-delay: 0.1s;">
                Money Quotient<br>
                <span class="text-[#4ECDC4]">MQ</span>
            </h1>
            <p class="text-xl md:text-2xl text-white/80 font-light leading-relaxed mb-10 animate-slideUp" style="animation-delay: 0.2s;">
                "원하는 삶을 숫자로 설계하고, 함께 실천하다."
            </p>
            
            <div class="max-w-2xl mx-auto space-y-6 text-white/70 text-lg leading-relaxed animate-slideUp" style="animation-delay: 0.3s;">
                <p>MQ는 <strong class="text-white font-medium">'돈을 다루는 지능'</strong>이라는 뜻처럼,<br class="hidden md:block"> 돈을 목적이 아닌 삶을 설계하는 도구로 바라봅니다.</p>
                <p>우리의 미션은 아이들과 부모가 함께,<br class="hidden md:block"> 경제를 통해 스스로를 이해하고 성장하는 여정을 만드는 것입니다.</p>
            </div>
        </div>
    </section>

    <!-- ===== Image Banner ===== -->
    <div class="container mx-auto px-4 max-w-5xl -mt-20 relative z-20 animate-slideUp" style="animation-delay: 0.4s;">
        <div class="rounded-3xl overflow-hidden shadow-2xl bg-[#2D3047] aspect-video relative group flex items-center justify-center">
             <div class="absolute inset-0 bg-gray-200 animate-pulse"></div>
             <img src="{{ asset('images/content/mq_introduce_banner_v4_1770826875020.png') }}" 
                  alt="MQ 커뮤니티 소개" 
                  class="w-full h-full object-contain relative z-10"
                  onload="this.previousElementSibling.style.display='none'">
        </div>
    </div>

    <!-- ===== Core Values Section ===== -->
    <section class="py-20 px-4">
        <div class="container mx-auto max-w-6xl">
            <div class="text-center mb-16 animate-slideUp" style="animation-delay: 0.5s;">
                <h2 class="font-outfit text-3xl md:text-4xl font-bold text-[#2D3047] mb-4">MQ는 이런 커뮤니티입니다</h2>
                <div class="w-16 h-1 bg-[#4ECDC4] mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6 lg:gap-8 max-w-5xl mx-auto">
                <!-- Card 1 -->
                <div class="feature-card animate-slideUp" style="animation-delay: 0.6s;">
                    <div class="w-14 h-14 bg-[#E6FFFA] rounded-2xl flex items-center justify-center text-3xl mb-6">🌱</div>
                    <h3 class="font-outfit text-2xl font-bold text-[#2D3047] mb-4">함께 성장하는 경제 커뮤니티</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        아이들은 돈을 통해 세상을 배우고, 부모는 그 성장을 지지합니다. 돈의 개념부터 자산, 가치, 진로까지 경제라는 렌즈로 삶 전반을 배웁니다.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="feature-card animate-slideUp" style="animation-delay: 0.7s;">
                    <div class="w-14 h-14 bg-[#FFF5F5] rounded-2xl flex items-center justify-center text-3xl mb-6">📚</div>
                    <h3 class="font-outfit text-2xl font-bold text-[#2D3047] mb-4">배우고 실천하는 프로그램</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        실시간 클래스, 미션형 게임으로 자기주도 학습을 이끌어냅니다. 오프라인에서는 보드게임, 세미나, 워크숍 등 체험 중심 프로그램이 준비되어 있습니다.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="feature-card animate-slideUp" style="animation-delay: 0.8s;">
                    <div class="w-14 h-14 bg-[#EBF8FF] rounded-2xl flex items-center justify-center text-3xl mb-6">🌍</div>
                    <h3 class="font-outfit text-2xl font-bold text-[#2D3047] mb-4">글로벌 그랜드투어</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        국내를 넘어 해외의 혁신적 교육 현장을 탐방하는 <span class="text-[#4ECDC4] font-semibold">'MQ 그랜드투어'</span>를 통해 더 넓은 시야와 동기를 선물합니다.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="feature-card animate-slideUp" style="animation-delay: 0.9s;">
                    <div class="w-14 h-14 bg-[#FFFAF0] rounded-2xl flex items-center justify-center text-3xl mb-6">🔥</div>
                    <h3 class="font-outfit text-2xl font-bold text-[#2D3047] mb-4">단단한 교육 철학</h3>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        자극적인 성과보다는 꾸준하고 본질적인 배움을 추구합니다. 아이들에게는 즐거운 자극을, 부모에게는 교육적 안심을 주는 커뮤니티입니다.
                    </p>
                </div>
            </div>
            
            <!-- Additional Value (Centered) -->
            <div class="mt-8 feature-card text-center max-w-4xl mx-auto animate-slideUp" style="animation-delay: 1.0s;">
                <div class="w-14 h-14 bg-[#F0F5FF] rounded-2xl flex items-center justify-center text-3xl mb-6 mx-auto">🌿</div>
                <h3 class="font-outfit text-2xl font-bold text-[#2D3047] mb-4">내면의 힘을 키우다</h3>
                <p class="text-gray-600 leading-relaxed max-w-2xl mx-auto">
                    우리는 돈을 버는 기술보다, 돈을 통해 <span class="font-semibold text-[#2D3047]">자기 삶을 주도하는 힘</span>을 키우는 데 집중합니다. 자기이해, 책임감, 가치 있는 선택을 함께 실천합니다.
                </p>
            </div>
        </div>
    </section>

    <!-- ===== Quotes Section ===== -->
    <section class="py-20 px-4 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#FF4D4D] via-[#4ECDC4] to-[#FFB347]"></div>
        
        <div class="container mx-auto max-w-5xl">
            <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
                <!-- Quote 1 -->
                <div class="bg-[#F8F9FA] p-8 md:p-10 rounded-3xl relative">
                    <span class="absolute top-6 left-6 text-6xl text-[#E2E8F0] font-serif">"</span>
                    <p class="relative z-10 text-gray-700 text-lg leading-relaxed italic mb-6 pt-4">
                        10대와 20대는 부자가 되는 기질이 형성되는 시기다. 부모라면 자녀가 돈과 친하게 지낼 수 있게 해줘야 한다. <br><span class="inline-block mt-2 font-semibold text-[#2D3047] not-italic highlight decoration-[#FF4D4D]/30 decoration-4 underline-offset-4">자녀에게 돈을 다루는 법을 가르쳐줘라.</span>
                    </p>
                    <div class="flex items-center gap-3 mt-4">
                        <div class="w-1 h-8 bg-[#FF4D4D] rounded-full"></div>
                        <p class="text-sm font-bold text-[#2D3047]">부자의 기술</p>
                    </div>
                </div>

                <!-- Quote 2 -->
                <div class="bg-[#F8F9FA] p-8 md:p-10 rounded-3xl relative">
                    <span class="absolute top-6 left-6 text-6xl text-[#E2E8F0] font-serif">"</span>
                    <p class="relative z-10 text-gray-700 text-lg leading-relaxed italic mb-6 pt-4">
                        투자의 성과는 '우리가 무엇을 사고파는가'에서 나오는 것이 아니라 <br><span class="inline-block mt-2 font-semibold text-[#2D3047] not-italic highlight decoration-[#4ECDC4]/30 decoration-4 underline-offset-4">'우리가 무엇을 보유하고 있는가'</span>에서 나온다.
                    </p>
                    <div class="flex items-center gap-3 mt-4">
                        <div class="w-1 h-8 bg-[#4ECDC4] rounded-full"></div>
                        <p class="text-sm font-bold text-[#2D3047]">하워드 막스</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA Section ===== -->
    <section class="py-24 px-4 bg-[#2D3047] text-white text-center relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="container mx-auto max-w-4xl relative z-10">
            <h2 class="font-outfit text-3xl md:text-5xl font-bold mb-8 leading-tight">
                숫자에 휘둘리지 않고,<br>
                <span class="text-[#FF4D4D]">숫자를 삶의 도구로 쓰는 사람</span>
            </h2>
            <p class="text-xl text-white/70 mb-12 font-light">
                MQ가 아이들의 성장을 위해 함께하겠습니다.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('course.l1.intro') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-[#FF4D4D] rounded-xl hover:bg-[#FF3333] transition-all shadow-[0_10px_20px_rgba(255,77,77,0.3)] hover:-translate-y-1 hover:shadow-[0_15px_30px_rgba(255,77,77,0.4)] group">
                    <span>코스 시작하기</span>
                    <svg class="w-5 h-5 ml-2 -mr-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
            </div>
        </div>
    </section>

</div>
@endsection