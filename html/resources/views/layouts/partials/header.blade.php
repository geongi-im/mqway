<!-- 햄버거 메뉴 배경 오버레이 -->
<div id="menuBackdrop" class="menu-backdrop fixed inset-0 bg-black bg-opacity-50 z-[100] opacity-0 invisible pointer-events-none transition-all duration-300"></div>

<!-- 좌측 사이드바 -->
<div id="sideMenu" class="side-menu fixed top-0 left-0 h-full w-80 bg-point z-[101] overflow-y-auto transform -translate-x-full transition-transform duration-300 ease-in-out">
    <button id="closeMenu" class="absolute top-4 right-4 text-cdark hover:text-cgray text-3xl">&times;</button>
    <nav class="pt-16 px-8">
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
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('course*') ? 'font-bold text-point1 border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="course">
                    <span>Course</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20" data-submenu="course">
                    <li class="relative">
                        <a href="{{ route('course.l1.intro') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('course.l1.intro') ? 'font-bold text-point1 bg-dark/10' : '' }}">
                            L1 코스
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="#" onclick="event.preventDefault(); alert('코스 준비중입니다.');"
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6">
                            L2 코스
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

/* 모바일 사이드 메뉴 스타일 */
.menu-backdrop.active {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}

.side-menu.active {
    transform: translateX(0) !important;
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

    /* PC에서 모바일 메뉴 완전히 숨김 */
    .menu-backdrop,
    .side-menu {
        display: none !important;
    }
    
    /* 프로필 드롭다운은 클릭으로만 동작 (호버 효과 제거) */
}

@media (max-width: 1023px) {
    /* 1023px 이하에서 모바일 메뉴만 표시 */
    .desktop-menu {
        display: none !important;
    }
    .mobile-menu-button {
        display: block !important;
    }

    /* 모바일 헤더 레이아웃 */
    .container.mx-auto {
        position: relative;
    }

    /* 모바일에서 로고 완전히 중앙 정렬 */
    .mobile-logo-center {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
    }
    
    /* 모바일 프로필 드롭다운 조정 */
    .profile-dropdown-mobile {
        right: 0;
        left: auto;
        min-width: 200px;
    }
    
    /* 모바일에서 사용자명 숨김 */
    @media (max-width: 767px) {
        .profile-username-mobile-hidden {
            display: none !important;
        }
        
        /* 모바일에서 프로필 이미지 크기 조정 */
        .profile-image-mobile {
            width: 32px !important;
            height: 32px !important;
        }
    }
}

/* PC에서 로고 초기화 */
@media (min-width: 1024px) {
    .lg\\:mobile-logo-center-reset {
        position: static !important;
        left: auto !important;
        transform: none !important;
    }
}
</style>

<!-- 헤더 -->
<nav class="bg-point p-4">
    <div class="container mx-auto flex justify-between items-center lg:justify-between">
        <!-- 모바일 햄버거 메뉴 (좌측에 고정) -->
        <button id="menuButton" class="mobile-menu-button text-cdark hover:text-cgray lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <!-- 로고 (모바일에서 중앙, PC에서 좌측) -->
        <a href="{{ url('/') }}" class="mobile-logo-center lg:mobile-logo-center-reset flex items-center">
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

            <!-- Course 드롭다운 -->
            <div class="relative group">
                <a href="{{ route('course.l1.intro') }}" class="px-3 py-2 font-medium flex items-center {{ request()->is('course*') ? 'font-bold text-point1' : 'text-white' }}">
                    Course
                    <svg class="w-4 h-4 ml-1 transform group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <div class="absolute top-full left-0 mt-4 w-56 bg-point2 shadow-lg rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a href="{{ route('course.l1.intro') }}" class="block px-4 py-3 {{ request()->routeIs('course.l1.intro') ? 'font-bold text-point1' : 'text-white' }}">
                         L1 코스
                    </a>
                    <a href="#" onclick="event.preventDefault(); alert('코스 준비중입니다.');" class="block px-4 py-3 text-white">
                        L2 코스
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
        
        <!-- 로그인/로그아웃 영역 (우측) -->
        <div class="flex items-center gap-4">
            @if(auth()->check())
                <!-- 프로필 이미지 드롭다운 -->
                <div class="relative profile-dropdown">
                    <button id="profileDropdownButton" class="flex items-center gap-2 lg:gap-3 p-1 rounded-full hover:bg-white/10 transition-colors">
                        <!-- 프로필 이미지 -->
                        <div class="w-8 h-8 lg:w-8 lg:h-8 profile-image-mobile rounded-full overflow-hidden border-2 border-gray-200">
                            @if(auth()->user()->mq_profile_image)
                                <img src="{{ asset('storage/uploads/profile/' . auth()->user()->mq_profile_image) }}" alt="프로필 이미지" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                    <svg class="w-4 h-4 lg:w-5 lg:h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <!-- 사용자명 (데스크톱에서만 표시) -->
                        <span class="text-white font-medium hidden md:block profile-username-mobile-hidden lg:inline">{{ auth()->user()->mq_user_name ?? auth()->user()->name }}</span>
                        <!-- 드롭다운 화살표 -->
                        <svg class="w-3 h-3 lg:w-4 lg:h-4 text-gray-300 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- 드롭다운 메뉴 -->
                    <div id="profileDropdownMenu" class="profile-dropdown-menu absolute top-full right-0 mt-2 w-48 profile-dropdown-mobile bg-white shadow-lg rounded-lg opacity-0 invisible transform translate-y-2 transition-all duration-200 z-50 border border-gray-200">
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ auth()->user()->mq_user_name ?? auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->mq_user_email ?? auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('mypage.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                마이페이지
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <a href="{{ url('/logout') }}" 
                               class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                로그아웃
                            </a>
                        </div>
                    </div>
                    
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            @else
                <a href="{{ url('/login') }}" class="text-cdark hover:text-cgray px-3 py-2 font-medium">
                    Login
                </a>
            @endif
        </div>
    </div>
</nav>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('menuButton');
    const menuBackdrop = document.getElementById('menuBackdrop');
    const sideMenu = document.getElementById('sideMenu');
    const closeMenuButton = document.getElementById('closeMenu');
    const toggleMenuButtons = document.querySelectorAll('.toggle-menu');
    
    // 프로필 드롭다운 관련 요소들
    const profileDropdownButton = document.getElementById('profileDropdownButton');
    const profileDropdownMenu = document.getElementById('profileDropdownMenu');
    
    // 각 메뉴 활성화 체크 (모바일 햄버거 메뉴용)
    const menuItems = [
        { path: '/guidebook', target: 'guidebook' },
        { path: '/cashflow', target: 'cashflow' },
        { path: '/tools', target: 'tools' },
        { path: '/board', target: 'board' },
        { path: '/course', target: 'course' }
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

    // 메뉴 열기 함수
    function openMenu() {
        if (menuBackdrop && sideMenu) {
            menuBackdrop.classList.add('active');
            sideMenu.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    // 메뉴 닫기 함수
    function closeMenu() {
        if (menuBackdrop && sideMenu) {
            menuBackdrop.classList.remove('active');
            sideMenu.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // 모바일 햄버거 메뉴 이벤트 (모바일에서만 동작)
    if (menuButton) {
        menuButton.addEventListener('click', openMenu);
    }

    // 닫기 버튼 클릭 시 메뉴 닫기
    if (closeMenuButton) {
        closeMenuButton.addEventListener('click', closeMenu);
    }

    // 배경 클릭 시 메뉴 닫기
    if (menuBackdrop) {
        menuBackdrop.addEventListener('click', closeMenu);
    }

    // ESC 키로 모바일 메뉴 닫기
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && (menuBackdrop.classList.contains('active') || sideMenu.classList.contains('active'))) {
            closeMenu();
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
            // PC 화면에서는 모바일 메뉴 닫기
            if (menuBackdrop.classList.contains('active') || sideMenu.classList.contains('active')) {
                closeMenu();
            }
        }
    });

    // 프로필 드롭다운 기능
    if (profileDropdownButton && profileDropdownMenu) {
        let isDropdownOpen = false;

        // 드롭다운 열기/닫기 토글
        function toggleProfileDropdown() {
            isDropdownOpen = !isDropdownOpen;
            if (isDropdownOpen) {
                profileDropdownMenu.classList.remove('opacity-0', 'invisible', 'translate-y-2');
                profileDropdownMenu.classList.add('opacity-100', 'visible', 'translate-y-0');
                profileDropdownButton.querySelector('svg:last-child').classList.add('rotate-180');
            } else {
                profileDropdownMenu.classList.add('opacity-0', 'invisible', 'translate-y-2');
                profileDropdownMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                profileDropdownButton.querySelector('svg:last-child').classList.remove('rotate-180');
            }
        }

        // 드롭다운 닫기
        function closeProfileDropdown() {
            if (isDropdownOpen) {
                isDropdownOpen = false;
                profileDropdownMenu.classList.add('opacity-0', 'invisible', 'translate-y-2');
                profileDropdownMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                profileDropdownButton.querySelector('svg:last-child').classList.remove('rotate-180');
            }
        }

        // 프로필 버튼 클릭 시 드롭다운 토글
        profileDropdownButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleProfileDropdown();
        });

        // 드롭다운 외부 클릭 시 닫기
        document.addEventListener('click', (e) => {
            if (!profileDropdownButton.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
                closeProfileDropdown();
            }
        });

        // ESC 키로 드롭다운 닫기
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isDropdownOpen) {
                closeProfileDropdown();
            }
        });

        // 화면 크기 변경 시 드롭다운 닫기
        window.addEventListener('resize', closeProfileDropdown);
    }
});
</script>
@endpush 