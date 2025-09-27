@extends('layouts.app')

@section('content')
<style>
    .introduce-banner {
        width: 100%;
        height: 50vh; /* 화면 높이의 50%로 설정, index.blade.php 참고 */
        background-color: #f0f0f0; /* 이미지 로딩 전 배경색 또는 이미지 주변 배경색 */
        overflow: hidden; /* 이미지가 컨테이너를 벗어나지 않도록 */
        display: flex; /* 이미지를 중앙 정렬하기 위함 */
        align-items: center;
        justify-content: center;
    }

    .introduce-banner img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* 이미지가 잘리지 않고 비율 유지하며 컨테이너에 맞춤 */
        object-position: center;
    }

    @media (min-width: 1024px) { /* PC 화면 크기 (예: 1024px 이상) */
        .introduce-banner {
            /* PC에서는 vh가 너무 클 수 있으므로, max-height로 제한하거나 vh 값을 줄일 수 있습니다. */
            /* 일단 50vh로 두고, 필요시 조정 */
        }
    }
</style>

<div class="introduce-banner">
    <img src="{{ asset('images/content/community_introduce_banner.png') }}" alt="커뮤니티 소개 배너">
</div>

<div class="container mx-auto px-4 py-12 sm:py-16 md:py-20">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12 md:mb-16">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800 mb-6">MQ (Money Quotient)</h1>
            <p class="text-xl sm:text-2xl font-semibold text-gray-700">
                "원하는 삶을 숫자로 설계하고, 함께 실천하다."
            </p>
        </div>

        <div class="space-y-8 text-gray-700 text-base sm:text-lg leading-relaxed">
            <p>우리는 MQ입니다.</p>
            <p>MQ는 Money Quotient, 즉 '돈을 다루는 지능'이라는 뜻처럼, 돈을 목적이 아니라 삶을 설계하는 도구로 바라봅니다.</p>
            <p>우리의 미션은 아이들과 그 부모가 함께, 돈을 통해 스스로를 이해하고 성장하는 여정을 만드는 것입니다.</p>

            <div class="mt-12 md:mt-16 pt-8">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-8 pb-3 border-b-2 border-point">
                    MQ는 이런 커뮤니티입니다:
                </h2>
                <div class="space-y-12">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 flex items-start sm:items-center">
                            <span class="text-3xl mr-3 mt-1 sm:mt-0">🌱</span>
                            <span>자녀와 함께 성장하는 경제 커뮤니티</span>
                        </h3>
                        <div class="space-y-3 pl-8 sm:pl-12">
                            <p>MQ는 10대 학생들과 그들의 부모님이 함께하는 커뮤니티입니다.</p>
                            <p>아이들은 돈을 통해 세상을 배우고, 부모는 그 성장을 옆에서 함께 지지합니다.</p>
                            <p>돈의 개념, 소비와 저축, 자산과 가치, 일과 진로까지—경제라는 렌즈를 통해 삶 전반을 배웁니다.</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 flex items-start sm:items-center">
                            <span class="text-3xl mr-3 mt-1 sm:mt-0">📚</span>
                            <span>배우고 실천하는 다양한 프로그램</span>
                        </h3>
                        <div class="space-y-3 pl-8 sm:pl-12">
                            <p>온라인에서는 실시간 클래스, 콘텐츠 기반 토론, 미션형 게임과 커뮤니티 활동을 통해 아이들의 흥미와 참여를 유도하고, 자기주도 학습을 이끌어냅니다.</p>
                            <p class="mt-2 font-medium">오프라인에서는</p>
                            <ul class="list-disc list-inside ml-4 space-y-1 mt-1 text-gray-600">
                                <li>경제 보드게임,</li>
                                <li>실전 세미나와 특강,</li>
                                <li>참여형 워크숍 등</li>
                            </ul>
                            <p class="mt-2">아이와 부모가 함께 즐기며 배울 수 있는 체험 중심 프로그램이 준비되어 있습니다.</p>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 flex items-start sm:items-center">
                            <span class="text-3xl mr-3 mt-1 sm:mt-0">🌍</span>
                            <span>해외 교육 탐방 '그랜드투어'</span>
                        </h3>
                        <div class="space-y-3 pl-8 sm:pl-12">
                            <p>MQ는 국내 교육에만 머무르지 않습니다.</p>
                            <p>아이들이 직접 해외의 유명 대학과 혁신적 교육기관을 탐방하며, 스스로 더 넓은 시야와 진로에 대한 동기를 얻을 수 있도록 <strong class="font-bold text-point">'MQ 그랜드투어 프로그램'</strong>을 운영하고 있습니다.</p>
                            <p>단순한 여행이 아닌, 진짜 배움과 성장의 여정입니다.</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 flex items-start sm:items-center">
                            <span class="text-3xl mr-3 mt-1 sm:mt-0">🔥</span>
                            <span>열정적이지만 교육적으로 단단하게</span>
                        </h3>
                        <div class="space-y-3 pl-8 sm:pl-12">
                            <p>MQ는 자극적이거나 단기적인 성과에 집중하지 않습니다.</p>
                            <p>아이들의 성장은 하루아침에 완성되지 않기에, 우리는 꾸준하고도 본질적인 배움을 추구합니다.</p>
                            <p>아이들에게는 즐거운 자극을, 부모에게는 교육적 안심을 주는 커뮤니티가 되겠습니다.</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 flex items-start sm:items-center">
                            <span class="text-3xl mr-3 mt-1 sm:mt-0">🌿</span>
                            <span>경제를 통해 내면의 힘을 키우다</span>
                        </h3>
                        <div class="space-y-3 pl-8 sm:pl-12">
                            <p>우리는 돈을 버는 기술보다, 돈을 통해 자기 삶을 주도하는 힘을 키우는 데 더 집중합니다.</p>
                            <p>자기이해, 책임감, 가치 있는 선택—이 모든 것을 경제교육이라는 통로로 함께 실천합니다.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 md:mt-16 pt-8 sm:pt-12 border-t-2 border-gray-200 text-center space-y-6">
                 <p class="text-lg sm:text-xl font-semibold text-gray-800">
                    당신의 자녀가 숫자에 휘둘리지 않고, <br />숫자를 삶의 도구로 쓰는 사람으로 자라길 바란다면.
                </p>
                 <p class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-point">
                    MQ가 함께하겠습니다.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection 