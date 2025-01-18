<!-- 햄버거 메뉴 오버레이 -->
<div id="menuOverlay" class="menu-overlay fixed inset-0 bg-dark z-50 flex flex-col items-center justify-center">
    <button id="closeMenu" class="absolute top-4 right-4 text-secondary text-3xl">&times;</button>
    <nav class="text-left w-full max-w-md px-8">
        <ul class="space-y-6 text-2xl">
            <li>
                <a href="{{ url('/') }}" class="text-secondary hover:text-secondary/80 block py-2 {{ request()->is('/') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Home
                </a>
            </li>
            <li class="relative">
                <a href="#" class="text-secondary hover:text-secondary/80 guidebook-toggle flex items-center justify-between py-2 {{ request()->is('guidebook*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    <span>Guidebook</span>
                    <svg class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu hidden space-y-1 ml-4 border-l-2 border-secondary/20">
                    <li class="relative">
                        <a href="{{ route('guidebook.life-search') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-secondary/70 hover:text-secondary transition-all duration-200 hover:pl-6 
                           {{ request()->routeIs('guidebook.life-search') ? 'text-secondary font-bold bg-dark/10' : '' }}">
                            원하는 삶 찾기
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                    <li class="relative">
                        <a href="{{ route('guidebook.reality-check') }}" 
                           class="submenu-item block py-2 pl-4 text-base text-secondary/70 hover:text-secondary transition-all duration-200 hover:pl-6
                           {{ request()->routeIs('guidebook.reality-check') ? 'text-secondary font-bold bg-dark/10' : '' }}">
                            현실점검
                            <span class="absolute left-0 top-1/2 transform -translate-y-1/2 w-1 h-0 bg-secondary transition-all duration-200"></span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--
            <li>
                <a href="{{ url('/pick') }}" class="text-secondary hover:text-secondary/80 block py-2 {{ request()->is('pick*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Pick
                </a>
            </li>
            <li>
                <a href="{{ url('/beecube') }}" class="text-secondary hover:text-secondary/80 block py-2 {{ request()->is('beecube*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Beecube
                </a>
            </li>
            -->
            <li>
                <a href="{{ url('/board') }}" class="text-secondary hover:text-secondary/80 block py-2 {{ request()->is('board*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    Board
                </a>
            </li>
            <li>
                <a href="{{ url('/news') }}" class="text-secondary hover:text-secondary/80 block py-2 {{ request()->is('news*') ? 'font-bold border-l-4 border-secondary pl-4 -ml-4' : '' }}">
                    News
                </a>
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
.guidebook-toggle.active svg {
    transform: rotate(180deg);
}

/* Active 상태일 때 서브메뉴 자동 표시 */
.guidebook-toggle.active + .submenu {
    display: block;
    max-height: 200px;
}
</style>

<!-- 헤더 -->
<nav class="bg-dark p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ asset('images/logo/mqway-white-logo.png') }}" alt="MQway" class="h-8">
        </a>
        <div class="flex items-center gap-4">
            @if(auth()->check())
                <span class="text-secondary">{{ auth()->user()->name }}</span>
                <a href="{{ url('/logout') }}" class="text-secondary hover:text-secondary/80"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    로그아웃
                </a>
                <form id="logout-form" action="{{ url('/logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <a href="{{ url('/login') }}" class="text-secondary hover:text-secondary/80">로그인</a>
            @endif
            <button id="menuButton" class="text-secondary hover:text-secondary/80">
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
    const guidebookToggle = document.querySelector('.guidebook-toggle');
    const submenu = document.querySelector('.submenu');

    // Guidebook 섹션이 active 상태일 때 자동으로 서브메뉴 표시
    if (window.location.pathname.includes('/guidebook')) {
        guidebookToggle.classList.add('active');
        submenu.classList.add('show');
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

    guidebookToggle.addEventListener('click', (e) => {
        e.preventDefault();
        submenu.classList.toggle('show');
        guidebookToggle.classList.toggle('active');
    });
});
</script>
@endpush 