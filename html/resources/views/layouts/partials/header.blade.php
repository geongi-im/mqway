<!-- 햄버거 메뉴 오버레이 -->
<div id="menuOverlay" class="menu-overlay fixed inset-0 bg-point z-[100] flex flex-col items-center justify-center">
    <button id="closeMenu" class="absolute top-4 right-4 text-cdark hover:text-cgray text-3xl">&times;</button>
    <nav class="text-left w-full max-w-md px-8">
        <ul class="space-y-6 text-2xl">
            <li>
                <a href="{{ url('/') }}" class="text-cdark hover:text-cgray block py-2 {{ request()->is('/') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Home
                </a>
            </li>
            <li>
                <a href="{{ route('introduce') }}" class="text-cdark hover:text-cgray block py-2 {{ request()->routeIs('introduce') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Introduce
                </a>
            </li>
            <li class="relative">
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('guidebook*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="guidebook">
                    <span>Guidebook</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu guidebook-submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20">
                    <li class="relative">
                        <a href="{{ route('guidebook.life-search') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('guidebook.life-search') ? 'font-bold bg-dark/10' : '' }}">
                            원하는 삶 찾기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('guidebook.reality-check') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('guidebook.reality-check') ? 'font-bold bg-dark/10' : '' }}">
                            현실 점검하기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('guidebook.roadmap') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('guidebook.roadmap') ? 'font-bold bg-dark/10' : '' }}">
                            로드맵 작성하기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="relative">
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('cashflow*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="cashflow">
                    <span>Cashflow</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu cashflow-submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20">
                    <li class="relative">
                        <a href="{{ route('cashflow.introduction') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('cashflow.introduction') ? 'font-bold bg-dark/10' : '' }}">
                            Cashflow 소개
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <!--
                    <li class="relative">
                        <a href="{{ route('cashflow.helper') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('cashflow.helper') ? 'font-bold bg-dark/10' : '' }}">
                            Cashflow 도우미
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    -->
                    <li class="relative">
                        <a href="{{ route('cashflow.process') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('cashflow.process') ? 'font-bold bg-dark/10' : '' }}">
                            Cashflow 진행
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="relative">
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('tools*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="tools">
                    <span>Tools</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu tools-submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20">
                    <li class="relative">
                        <a href="{{ route('tools.economy-term-game') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('tools.economy-term-game') ? 'font-bold bg-dark/10' : '' }}">
                            경제 용어 카드 맞추기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('tools.financial-quiz') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('tools.financial-quiz') ? 'font-bold bg-dark/10' : '' }}">
                            경제 상식 퀴즈
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('tools.retirement-calculator') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('tools.retirement-calculator') ? 'font-bold bg-dark/10' : '' }}">
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
                <a href="#" class="text-cdark hover:text-cgray toggle-menu flex items-center justify-between py-2 {{ request()->is('board*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}" data-target="board">
                    <span>Board</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu board-submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20">
                    <li class="relative">
                        <a href="{{ route('board-content.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('board-content.index') ? 'font-bold bg-dark/10' : '' }}">
                            추천 콘텐츠
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('board-research.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('board-research.index') ? 'font-bold bg-dark/10' : '' }}">
                            투자 리서치
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('board-video.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('board-video.index') ? 'font-bold bg-dark/10' : '' }}">
                            쉽게 보는 경제
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('board-news.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('board-news.index') ? 'font-bold bg-dark/10' : '' }}">
                            뉴스 게시판
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('board-portfolio.index') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-cdark hover:text-cgray transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('board-portfolio.index') ? 'font-bold bg-dark/10' : '' }}">
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
</style>

<!-- 헤더 -->
<nav class="bg-point p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('images/logo/mqway_blank_logo.png') }}" alt="MQWAY" class="h-8">
        </a>
        <div class="flex items-center gap-4">
            @if(auth()->check())
                <span class="text-secondary">{{ auth()->user()->name }}</span>
                <a href="{{ url('/logout') }}" 
                   class="text-cdark hover:text-cgray group relative"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                    </svg>
                    <span class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-cdark text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-[60]">로그아웃</span>
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <a href="{{ url('/login') }}" class="text-cdark hover:text-cgray group relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    <span class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-cdark text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-[60]">로그인</span>
                </a>
            @endif
            <button id="menuButton" class="text-cdark hover:text-cgray">
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
    
    // 각 메뉴 활성화 체크
    if (window.location.pathname.includes('/guidebook')) {
        document.querySelector('[data-target="guidebook"]').classList.add('active');
        document.querySelector('.guidebook-submenu').classList.add('show');
    }

    if (window.location.pathname.includes('/cashflow')) {
        document.querySelector('[data-target="cashflow"]').classList.add('active');
        document.querySelector('.cashflow-submenu').classList.add('show');
    }

    if (window.location.pathname.includes('/tools')) {
        document.querySelector('[data-target="tools"]').classList.add('active');
        document.querySelector('.tools-submenu').classList.add('show');
    }
    
    if (window.location.pathname.includes('/board')) {
        document.querySelector('[data-target="board"]').classList.add('active');
        document.querySelector('.board-submenu').classList.add('show');
    }

    menuButton.addEventListener('click', () => {
        menuOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    closeMenu.addEventListener('click', () => {
        menuOverlay.classList.remove('active');
        document.body.style.overflow = '';
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            menuOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // 모든 토글 메뉴 버튼에 이벤트 리스너 추가
    toggleMenuButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const target = button.getAttribute('data-target');
            const submenu = document.querySelector(`.${target}-submenu`);
            
            submenu.classList.toggle('show');
            button.classList.toggle('active');
        });
    });
});
</script>
@endpush 