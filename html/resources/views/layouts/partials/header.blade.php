<!-- 햄버거 메뉴 오버레이 -->
<div id="menuOverlay" class="menu-overlay fixed inset-0 bg-point z-[100] flex flex-col items-center justify-center">
    <button id="closeMenu" class="absolute top-4 right-4 text-cdark hover:text-cgray text-3xl">&times;</button>
    <nav class="text-left w-full max-w-md px-8">
        <ul class="space-y-6 text-2xl">
            <!--
            <li>
                <a href="{{ url('/') }}" class="text-cdark hover:text-cgray block py-2 {{ request()->is('/') ? 'font-bold text-point1 border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Home
                </a>
            </li>
            -->
            <li>
                <a href="{{ route('introduce') }}" class="text-cdark hover:text-cgray block py-2 {{ request()->routeIs('introduce') ? 'font-bold text-point1 border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Introduce
                </a>
            </li>
            <li class="relative">
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('course-l1*') ? 'font-bold text-point1 border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="course-l1">
                    <span>Course L1</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20" data-submenu="course-l1">
                    <li class="relative">
                        <a href="{{ route('course-l1.intro') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('guidebook.life-search') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            코스 소개
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('course-l1.life-map') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('course-l1.life-map') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            나의 인생 지도 그리기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('guidebook*') ? 'font-bold text-point1 border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="guidebook">
                    <span>Guidebook</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20" data-submenu="guidebook">
                    <li class="relative">
                        <a href="{{ route('guidebook.life-search') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('guidebook.life-search') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            원하는 삶 찾기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('guidebook.reality-check') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('guidebook.reality-check') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            현실 점검하기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('guidebook.roadmap') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('guidebook.roadmap') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            로드맵 작성하기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('cashflow*') ? 'font-bold text-point1 border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="cashflow">
                    <span>Cashflow</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20" data-submenu="cashflow">
                    <li class="relative">
                        <a href="{{ route('cashflow.intro') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('cashflow.intro') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            Cashflow 소개
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('cashflow.helper') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('cashflow.helper') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            Cashflow 도우미
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('tools*') ? 'font-bold text-point1 border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="tools">
                    <span>Tools</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20" data-submenu="tools">
                    <li class="relative">
                        <a href="{{ route('tools.economy-term-game') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('tools.economy-term-game') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            경제 용어 카드 맞추기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('tools.financial-quiz') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('tools.financial-quiz') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            경제 상식 퀴즈
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('tools.retirement-calculator') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('tools.retirement-calculator') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            노후 자금 계산기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                </ul>
            </li>

            <!--
            <li>
                <a href="{{ url('/pick') }}" class="text-point hover:text-secondary/80 block py-2 {{ request()->is('pick*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Pick
                </a>
            </li>
            <li>
                <a href="{{ url('/beecube') }}" class="text-point hover:text-secondary/80 block py-2 {{ request()->is('beecube*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Beecube
                </a>
            </li>
            -->
            <li class="relative">
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('board*') ? 'font-bold text-point1 border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="board">
                    <span>Board</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20" data-submenu="board">
                    <li class="relative">
                        <a href="{{ route('board-content.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('board-content.index') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            추천 콘텐츠
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('board-research.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('board-research.index') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            투자 리서치
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('board-video.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('board-video.index') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            쉽게 보는 경제
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('board-news.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('board-news.index') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            뉴스 게시판
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('board-portfolio.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('board-portfolio.index') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            투자대가의 포트폴리오
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                </ul>
            </li>
            
        </ul>
    </nav>
</div>

<style>
/* 모바일 햄버거 메뉴 관련 스타일 */
.submenu {
    transition: all 0.3s ease-in-out;
    max-height: 0;
    overflow: hidden;
}
.submenu.show {
    display: block;
    max-height: 200px;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}
.submenu-item:hover .absolute {
    height: 70%;
}
.toggle-menu.active svg {
    transform: rotate(180deg);
}

/* Active 상태일 때 서브메뉴 자동 표시 */
.toggle-menu.active + .submenu {
    display: block;
    max-height: 250px;
}

/* 메뉴 오버레이가 활성화되었을 때 하단 요소들과 상호작용 차단 */
.menu-overlay.active {
    pointer-events: auto;
}

.menu-overlay.active ~ nav {
    pointer-events: none;
}

/* PC용 드롭다운 메뉴 스타일 개선 */
@media (min-width: 1024px) {
    /* 1024px 이상에서 PC 메뉴 표시 */
    .desktop-menu {
        display: flex !important;
    }
    .mobile-menu-button {
        display: none !important;
    }
    
    /* 드롭다운 메뉴 hover 효과 개선 */
    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .group .group-hover\:opacity-100 {
        transform: translateY(-10px);
        transition: all 0.2s ease-in-out;
    }
    
    /* 드롭다운 메뉴 화살표 애니메이션 */
    .group:hover .group-hover\:rotate-180 {
        transform: rotate(180deg);
    }
}

@media (max-width: 1023px) {
    /* 1023px 이하에서 모바일 메뉴만 표시 */
    .desktop-menu {
        display: none !important;
    }
    .mobile-menu-button {
        display: block !important;
    }
}
</style>

<!-- 헤더 -->
<nav class="bg-point p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('images/logo/logo-w300-h63.png') }}" alt="MQWAY" class="h-8">
        </a>
        
        <!-- PC용 메뉴 (1024px 이상에서만 표시) -->
        <div class="desktop-menu hidden items-center space-x-2">
            <!--
            <a href="{{ url('/') }}" class="px-3 py-2 font-medium {{ request()->is('/') ? 'font-bold text-point1' : 'text-white' }}">
                Home
            </a>
            -->
            <a href="{{ route('introduce') }}" class="px-3 py-2 font-medium {{ request()->routeIs('introduce') ? 'font-bold text-point1' : 'text-white' }}">
                Introduce
            </a>

            <!-- Course L1 드롭다운 -->
            <div class="relative group">
                <a href="{{ route('course-l1.intro') }}" class="px-3 py-2 font-medium flex items-center {{ request()->is('course-l1*') ? 'font-bold text-point1' : 'text-white' }}">
                    Course L1
                    <svg class="w-4 h-4 ml-1 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <div class="absolute top-full left-0 mt-4 w-56 bg-point2 shadow-lg rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a href="{{ route('course-l1.intro') }}" class="block px-4 py-3 {{ request()->routeIs('course-l1.intro') ? 'font-bold text-point1' : 'text-white' }}">
                         코스 소개
                    </a>
                    <a href="{{ route('guidebook.life-search') }}" class="block px-4 py-3 {{ request()->routeIs('guidebook.life-search') ? 'font-bold text-point1' : 'text-white' }}">
                         나의 인생 지도 그리기
                    </a>
                </div>
            </div>
            
            <!-- Guidebook 드롭다운 -->
            <div class="relative group">
                <a href="{{ route('guidebook.life-search') }}" class="px-3 py-2 font-medium flex items-center {{ request()->is('guidebook*') ? 'font-bold text-point1' : 'text-white' }}">
                    Guidebook
                    <svg class="w-4 h-4 ml-1 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <div class="absolute top-full left-0 mt-4 w-56 bg-point2 shadow-lg rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a href="{{ route('guidebook.life-search') }}" class="block px-4 py-3 {{ request()->routeIs('guidebook.life-search') ? 'font-bold text-point1' : 'text-white' }}">
                        원하는 삶 찾기
                    </a>
                    <a href="{{ route('guidebook.reality-check') }}" class="block px-4 py-3 {{ request()->routeIs('guidebook.reality-check') ? 'font-bold text-point1' : 'text-white' }}">
                        현실 점검하기
                    </a>
                    <a href="{{ route('guidebook.roadmap') }}" class="block px-4 py-3 {{ request()->routeIs('guidebook.roadmap') ? 'font-bold text-point1' : 'text-white' }}">
                        로드맵 작성하기
                    </a>
                </div>
            </div>
            
            <!-- Cashflow 드롭다운 -->
            <div class="relative group">
                <a href="{{ route('cashflow.intro') }}" class="px-3 py-2 font-medium flex items-center {{ request()->is('cashflow*') ? 'font-bold text-point1' : 'text-white' }}">
                    Cashflow
                    <svg class="w-4 h-4 ml-1 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <div class="absolute top-full left-0 mt-4 w-56 bg-point2 shadow-lg rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a href="{{ route('cashflow.intro') }}" class="block px-4 py-3 {{ request()->routeIs('cashflow.intro') ? 'font-bold text-point1' : 'text-white' }}">
                        Cashflow 소개
                    </a>
                    <a href="{{ route('cashflow.helper') }}" class="block px-4 py-3 {{ request()->routeIs('cashflow.helper') ? 'font-bold text-point1' : 'text-white' }}">
                        Cashflow 도우미
                    </a>
                </div>
            </div>
            
            <!-- Tools 드롭다운 -->
            <div class="relative group">
                <a href="{{ route('tools.economy-term-game') }}" class="px-3 py-2 font-medium flex items-center {{ request()->is('tools*') ? 'font-bold text-point1' : 'text-white' }}">
                    Tools
                    <svg class="w-4 h-4 ml-1 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <div class="absolute top-full left-0 mt-4 w-56 bg-point2 shadow-lg rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a href="{{ route('tools.economy-term-game') }}" class="block px-4 py-3 {{ request()->routeIs('tools.economy-term-game') ? 'font-bold text-point1' : 'text-white' }}">
                        경제 용어 카드 맞추기
                    </a>
                    <a href="{{ route('tools.financial-quiz') }}" class="block px-4 py-3 {{ request()->routeIs('tools.financial-quiz') ? 'font-bold text-point1' : 'text-white' }}">
                        경제 상식 퀴즈
                    </a>
                    <a href="{{ route('tools.retirement-calculator') }}" class="block px-4 py-3 {{ request()->routeIs('tools.retirement-calculator') ? 'font-bold text-point1' : 'text-white' }}">
                        노후 자금 계산기
                    </a>
                </div>
            </div>
            
            <!-- Board 드롭다운 -->
            <div class="relative group">
                <a href="{{ route('board-content.index') }}" class="px-3 py-2 font-medium flex items-center {{ request()->is('board*') ? 'font-bold text-point1' : 'text-white' }}">
                    Board
                    <svg class="w-4 h-4 ml-1 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <div class="absolute top-full left-0 mt-4 w-56 bg-point2 shadow-lg rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a href="{{ route('board-content.index') }}" class="block px-4 py-3 {{ request()->routeIs('board-content.*') ? 'font-bold text-point1' : 'text-white' }}">
                        추천 콘텐츠
                    </a>
                    <a href="{{ route('board-research.index') }}" class="block px-4 py-3 {{ request()->routeIs('board-research.*') ? 'font-bold text-point1' : 'text-white' }}">
                        투자 리서치
                    </a>
                    <a href="{{ route('board-video.index') }}" class="block px-4 py-3 {{ request()->routeIs('board-video.*') ? 'font-bold text-point1' : 'text-white' }}">
                        쉽게 보는 경제
                    </a>
                    <a href="{{ route('board-news.index') }}" class="block px-4 py-3 {{ request()->routeIs('board-news.*') ? 'font-bold text-point1' : 'text-white' }}">
                        뉴스 게시판
                    </a>
                    <a href="{{ route('board-portfolio.index') }}" class="block px-4 py-3 {{ request()->routeIs('board-portfolio.*') ? 'font-bold text-point1' : 'text-white' }}">
                        투자대가의 포트폴리오
                    </a>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            @if(auth()->check())
                <span class="text-secondary">{{ auth()->user()->name }}</span>
                <a href="{{ url('/logout') }}" 
                   class="text-cdark hover:text-cgray px-3 py-2 font-medium"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <a href="{{ url('/login') }}" class="text-cdark hover:text-cgray px-3 py-2 font-medium">
                    Login
                </a>
            @endif
            <!-- 햄버거 메뉴는 모바일에서만 표시 -->
            <button id="menuButton" class="mobile-menu-button text-cdark hover:text-cgray">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
    </div>
</nav>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('menuButton');
    const menuOverlay = document.getElementById('menuOverlay');
    const closeMenu = document.getElementById('closeMenu');
    const toggleMenuButtons = document.querySelectorAll('.toggle-menu');
    
    // 각 메뉴 활성화 체크 (모바일 햄버거 메뉴용)
    const menuItems = [
        { path: '/guidebook', target: 'guidebook' },
        { path: '/cashflow', target: 'cashflow' },
        { path: '/tools', target: 'tools' },
        { path: '/board', target: 'board' },
        { path: '/course-l1', target: 'course-l1' }
    ];

    menuItems.forEach(item => {
        if (window.location.pathname.includes(item.path)) {
            const toggle = document.querySelector(`[data-target="${item.target}"]`);
            const submenu = document.querySelector(`[data-submenu="${item.target}"]`);
            if (toggle && submenu) {
                toggle.classList.add('active');
                submenu.classList.add('show');
            }
        }
    });

    // 모바일 햄버거 메뉴 이벤트 (모바일에서만 동작)
    if (menuButton) {
        menuButton.addEventListener('click', () => {
            menuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (closeMenu) {
        closeMenu.addEventListener('click', () => {
            menuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // ESC 키로 모바일 메뉴 닫기
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menuOverlay.classList.contains('active')) {
            menuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // 모바일 햄버거 메뉴의 토글 기능
    toggleMenuButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const target = button.getAttribute('data-target');
            const submenu = document.querySelector(`[data-submenu="${target}"]`);
            
            if (submenu) {
                submenu.classList.toggle('show');
                button.classList.toggle('active');
            }
        });
    });

    // 화면 크기 변경 시 모바일 메뉴 상태 초기화
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            // PC 화면에서는 모바일 메뉴 오버레이 닫기
            if (menuOverlay.classList.contains('active')) {
                menuOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });
});
</script>
@endpush 