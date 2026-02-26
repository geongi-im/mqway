@extends('layouts.app')

@section('content')
<!-- ===== Hero Section ===== -->
<section class="relative pt-32 pb-24 overflow-hidden bg-[#3D4148]">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#3D4148] via-[#2D3047] to-[#1A1C29] opacity-95"></div>
        <div class="absolute top-0 right-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 brightness-100 contrast-150"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#4ECDC4] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#FF4D4D] rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10 text-center animate-slideUp">
        <a href="{{ route('mypage.mapping') }}" class="inline-flex items-center text-gray-400 hover:text-white mb-6 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center mr-2 group-hover:bg-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            MQ 맵핑으로 돌아가기
        </a>
        <br />
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-4 backdrop-blur-md">
            🎯 Vision Board
        </span>
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 leading-tight tracking-tight">
            나의 비전보드
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto leading-relaxed font-light">
            꿈을 캔버스에 자유롭게 펼쳐보세요.<br class="hidden md:block">
            드래그하고, 크기를 조절하고, 나만의 비전보드를 완성하세요.
        </p>
    </div>
</section>

<div class="container mx-auto px-4 -mt-10 relative z-20 max-w-6xl pb-12">

    <!-- 툴바 -->
    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-5 mb-6 animate-slideUp" style="animation-delay: 0.2s;">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <!-- 왼쪽: 편집 도구 -->
            <div class="flex items-center gap-2 flex-wrap">
                <!-- 배경 선택 -->
                <div class="relative">
                    <button id="bg-theme-btn" class="inline-flex items-center gap-1.5 text-sm bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-all font-medium" title="배경 테마 변경">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">배경</span>
                    </button>
                    <div id="bg-theme-dropdown" class="hidden absolute top-full left-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 p-3 z-30 w-48">
                        <p class="text-xs font-semibold text-gray-400 uppercase mb-2 px-1">배경 테마</p>
                        <button class="bg-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-all text-sm text-left" data-bg="white">
                            <span class="w-6 h-6 rounded-full bg-white border border-gray-200 flex-shrink-0"></span>
                            화이트
                        </button>
                        <button class="bg-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-all text-sm text-left" data-bg="dark">
                            <span class="w-6 h-6 rounded-full bg-[#2D3047] flex-shrink-0"></span>
                            다크
                        </button>
                        <button class="bg-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-all text-sm text-left" data-bg="cork">
                            <span class="w-6 h-6 rounded-full bg-[#C4A882] flex-shrink-0"></span>
                            코르크보드
                        </button>
                        <button class="bg-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-all text-sm text-left" data-bg="gradient">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-br from-[#4ECDC4] to-[#FF6B6B] flex-shrink-0"></span>
                            그라데이션
                        </button>
                    </div>
                </div>

                <!-- 텍스트 추가 -->
                <button id="add-text-btn" class="inline-flex items-center gap-1.5 text-sm bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-all font-medium" title="텍스트 추가">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span class="hidden sm:inline">텍스트</span>
                </button>

                <!-- 되돌리기 -->
                <button id="undo-btn" class="inline-flex items-center gap-1.5 text-sm bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-all font-medium" title="되돌리기 (Ctrl+Z)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                    <span class="hidden sm:inline">되돌리기</span>
                </button>

                <!-- 선택 삭제 -->
                <button id="delete-selected-btn" class="inline-flex items-center gap-1.5 text-sm bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-red-50 hover:text-red-500 transition-all font-medium" title="선택 항목 삭제 (Delete)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span class="hidden sm:inline">삭제</span>
                </button>

                <div class="hidden sm:block w-px h-6 bg-gray-200"></div>

                <!-- 전체 배치 -->
                <button id="auto-arrange-btn" class="inline-flex items-center gap-1.5 text-sm bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-all font-medium" title="자동 배치">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                    <span class="hidden sm:inline">자동 배치</span>
                </button>
            </div>

            <!-- 오른쪽: 비전보드 저장 + 이미지 저장 -->
            <div class="flex items-center gap-2">
                <button id="save-board-btn" class="inline-flex items-center gap-1.5 text-sm bg-gradient-to-r from-[#FF4D4D] to-[#e03e3e] text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    비전보드 저장
                </button>
                <button id="download-btn" class="inline-flex items-center gap-1.5 text-sm bg-gradient-to-r from-[#4ECDC4] to-[#2AA9A0] text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    이미지 저장
                </button>
            </div>
        </div>
    </div>

    <!-- Fabric.js 캔버스 영역 -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6 animate-slideUp" style="animation-delay: 0.3s;">
        <div id="canvas-wrapper" class="relative w-full overflow-auto" style="max-height: 70vh;">
            <div id="canvas-inner" style="transform-origin: 0 0;">
                <canvas id="vision-canvas"></canvas>
            </div>

            <!-- 줌 컨트롤 -->
            <div id="zoom-controls" class="absolute bottom-3 right-3 flex items-center gap-1 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg border border-gray-200 p-1 z-20">
                <button id="zoom-out-btn" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 transition-colors text-gray-600" title="축소">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </button>
                <span id="zoom-level" class="text-xs text-gray-500 font-medium min-w-[40px] text-center">100%</span>
                <button id="zoom-in-btn" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 transition-colors text-gray-600" title="확대">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
                <div class="w-px h-5 bg-gray-200"></div>
                <button id="zoom-fit-btn" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 transition-colors text-gray-600" title="화면에 맞추기">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                </button>
            </div>

            <!-- 빈 상태 오버레이 -->
            @if(count($selectedItems) === 0)
            <div id="empty-state" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50/80 z-10">
                <div class="text-center max-w-sm">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-[#4ECDC4]/20 to-[#FF4D4D]/20 flex items-center justify-center mx-auto mb-5">
                        <svg class="w-10 h-10 text-[#4ECDC4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#2D3047] mb-2">아직 목표가 없어요</h3>
                    <p class="text-sm text-gray-400 mb-5">MQ 맵핑에서 나의 목표를 선택하면<br>여기에 비전보드가 완성됩니다!</p>
                    <a href="{{ route('mypage.mapping') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#FF4D4D] to-[#e03e3e] text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition-all font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        목표 선택하러 가기
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- 내 목표 패널 (하단) -->
    @if(count($selectedItems) > 0)
    <div class="bg-white rounded-2xl shadow-xl p-5 animate-slideUp" style="animation-delay: 0.4s;">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#4ECDC4] to-[#2AA9A0] flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <span class="text-sm font-bold text-[#2D3047]">내 목표 <span class="text-gray-400 font-normal">(클릭하여 캔버스에 추가)</span></span>
            </div>
            <button id="add-all-btn" class="text-xs bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-all font-medium">
                전체 추가
            </button>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar scrollbar-hide" id="goals-panel">
            @foreach($selectedItems as $item)
            <div class="goal-thumbnail flex-shrink-0 cursor-pointer group relative" 
                 data-id="{{ $item['id'] }}" 
                 data-description="{{ $item['description'] }}" 
                 data-year="{{ $item['targetYear'] }}" 
                 data-category="{{ $item['category'] }}"
                 data-image="{{ $item['imageSrc'] ?? '' }}"
                 title="{{ $item['description'] }} ({{ $item['targetYear'] }}년)">
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-xl overflow-hidden border-2 border-gray-100 group-hover:border-[#4ECDC4] transition-all shadow-sm group-hover:shadow-md">
                    @if($item['imageSrc'])
                        <img src="{{ $item['imageSrc'] }}" alt="{{ $item['description'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <p class="text-[10px] md:text-xs text-center text-gray-500 mt-1.5 max-w-20 md:max-w-24 truncate">{{ $item['description'] }}</p>
                <span class="absolute -top-1 -right-1 bg-[#FF4D4D] text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold opacity-0 group-hover:opacity-100 transition-opacity">{{ $item['targetYear'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Fabric.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== 카테고리별 색상 매핑 =====
    const categoryColors = {
        creation: '#FF6B6B',
        adventure: '#4ECDC4',
        challenge: '#FFB347',
        growth: '#77DD77',
        experience: '#B19CD9',
        custom: '#FF4D4D'
    };
    const categoryLabels = @json($categoryLabels);
    const savedCanvasData = @json($canvasData);

    // ===== 캔버스 초기화 (고정 크기) =====
    const canvasWrapper = document.getElementById('canvas-wrapper');
    const canvasInner = document.getElementById('canvas-inner');
    const CANVAS_W = 1200;
    const CANVAS_H = 780;

    const canvas = new fabric.Canvas('vision-canvas', {
        width: CANVAS_W,
        height: CANVAS_H,
        backgroundColor: '#FFFFFF',
        selection: true,
        preserveObjectStacking: true
    });

    // ===== Undo 히스토리 =====
    let history = [];
    let historyIndex = -1;

    function saveState() {
        const json = JSON.stringify(canvas.toJSON());
        historyIndex++;
        history = history.slice(0, historyIndex);
        history.push(json);
        if (history.length > 30) {
            history.shift();
            historyIndex--;
        }
    }

    function undo() {
        if (historyIndex > 0) {
            historyIndex--;
            canvas.loadFromJSON(history[historyIndex], () => {
                canvas.renderAll();
            });
        }
    }

    // 초기 상태 저장
    saveState();

    // ===== DB 수동 저장 =====
    function saveToDB() {
        const saveBoardBtn = document.getElementById('save-board-btn');
        const originalHTML = saveBoardBtn.innerHTML;
        saveBoardBtn.disabled = true;
        saveBoardBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> 저장 중...';

        const jsonData = JSON.stringify(canvas.toJSON());
        fetch('{{ route("mypage.vision-board.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ canvas_data: jsonData })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                saveBoardBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 저장 완료!';
                setTimeout(() => { saveBoardBtn.innerHTML = originalHTML; saveBoardBtn.disabled = false; }, 2000);
            } else {
                saveBoardBtn.innerHTML = originalHTML;
                saveBoardBtn.disabled = false;
                alert('저장에 실패했습니다.');
            }
        })
        .catch(() => {
            saveBoardBtn.innerHTML = originalHTML;
            saveBoardBtn.disabled = false;
            alert('저장 중 오류가 발생했습니다.');
        });
    }

    document.getElementById('save-board-btn').addEventListener('click', saveToDB);

    // 변경 시 Undo 상태만 저장
    canvas.on('object:modified', saveState);
    canvas.on('object:added', function() {
        clearTimeout(canvas._saveTimeout);
        canvas._saveTimeout = setTimeout(saveState, 200);
    });
    canvas.on('object:removed', saveState);

    // ===== 초기 자동 배치 (페이지 로드 시 모든 목표를 캔버스에 배치) =====
    function autoPlaceAllGoals() {
        const thumbs = document.querySelectorAll('.goal-thumbnail');
        const total = thumbs.length;
        if (total === 0) return;

        // 그리드 배치 계산
        const cols = Math.ceil(Math.sqrt(total));
        const rows = Math.ceil(total / cols);
        const cellW = canvas.width / cols;
        const cellH = canvas.height / rows;

        thumbs.forEach((thumb, i) => {
            const item = {
                id: thumb.dataset.id,
                description: thumb.dataset.description,
                year: thumb.dataset.year,
                category: thumb.dataset.category,
                image: thumb.dataset.image
            };

            const col = i % cols;
            const row = Math.floor(i / cols);
            const x = cellW * col + cellW / 2;
            const y = cellH * row + cellH / 2;

            // 순차적으로 추가 (이미지 로딩 간격)
            setTimeout(() => addGoalToCanvas(item, x, y), i * 150);
        });
    }

    // 페이지 로드: 저장된 데이터가 있으면 복원, 없으면 자동 배치
    if (savedCanvasData) {
        try {
            const parsed = typeof savedCanvasData === 'string' ? JSON.parse(savedCanvasData) : savedCanvasData;
            canvas.loadFromJSON(parsed, function() {
                canvas.renderAll();
                saveState();
            });
        } catch(e) {
            console.error('비전보드 복원 실패:', e);
            setTimeout(autoPlaceAllGoals, 300);
        }
    } else {
        setTimeout(autoPlaceAllGoals, 300);
    }

    // ===== 목표 카드를 캔버스에 추가하는 함수 =====
    function addGoalToCanvas(item, x, y) {
        const cardWidth = window.innerWidth < 768 ? 140 : 180;
        const cardHeight = cardWidth * 1.2;
        const color = categoryColors[item.category] || '#4ECDC4';
        const yearText = item.year ? item.year + '년' : '';
        const desc = item.description || '';

        if (item.image) {
            // 이미지가 있는 경우
            fabric.Image.fromURL(item.image, function(img) {
                if (!img) {
                    // 이미지 로드 실패 시 플레이스홀더 카드 생성
                    createPlaceholderCard(item, x, y, cardWidth, cardHeight, color, desc, yearText);
                    return;
                }

                // 이미지 크기 조절
                const scale = Math.max(cardWidth / img.width, cardHeight / img.height);
                img.set({
                    scaleX: scale,
                    scaleY: scale,
                    originX: 'center',
                    originY: 'center'
                });

                // 이미지를 카드 크기로 클리핑
                img.set({
                    clipPath: new fabric.Rect({
                        width: cardWidth / scale,
                        height: cardHeight / scale,
                        originX: 'center',
                        originY: 'center',
                        rx: 12 / scale,
                        ry: 12 / scale
                    })
                });

                // 배경 카드 (라운드 코너 + 그림자 느낌)
                const bgRect = new fabric.Rect({
                    width: cardWidth,
                    height: cardHeight,
                    rx: 12,
                    ry: 12,
                    fill: '#f3f4f6',
                    originX: 'center',
                    originY: 'center',
                    stroke: color,
                    strokeWidth: 2
                });

                // 하단 그라데이션 오버레이
                const overlayRect = new fabric.Rect({
                    width: cardWidth,
                    height: cardHeight * 0.45,
                    rx: 0,
                    ry: 0,
                    originX: 'center',
                    originY: 'bottom',
                    top: cardHeight / 2,
                    fill: new fabric.Gradient({
                        type: 'linear',
                        coords: { x1: 0, y1: 0, x2: 0, y2: cardHeight * 0.45 },
                        colorStops: [
                            { offset: 0, color: 'rgba(0,0,0,0)' },
                            { offset: 1, color: 'rgba(0,0,0,0.75)' }
                        ]
                    })
                });

                // 설명 텍스트 (자동 줄바꿈)
                const descText = new fabric.Textbox(desc, {
                    fontSize: 13,
                    fontFamily: 'Noto Sans KR, sans-serif',
                    fill: '#ffffff',
                    fontWeight: '600',
                    width: cardWidth - 16,
                    originX: 'center',
                    originY: 'bottom',
                    top: cardHeight / 2 - 8,
                    textAlign: 'center',
                    shadow: '0 1px 3px rgba(0,0,0,0.6)',
                    splitByGrapheme: true
                });

                // 연도 뱃지
                const yearBadge = new fabric.Text(yearText, {
                    fontSize: 10,
                    fontFamily: 'Noto Sans KR, sans-serif',
                    fill: '#ffffff',
                    fontWeight: '700',
                    originX: 'right',
                    originY: 'top',
                    left: cardWidth / 2 - 8,
                    top: -cardHeight / 2 + 8,
                    backgroundColor: color,
                    padding: 4
                });

                // 그룹으로 묶기
                const group = new fabric.Group([bgRect, img, overlayRect, descText, yearBadge], {
                    left: x,
                    top: y,
                    originX: 'center',
                    originY: 'center',
                    cornerColor: color,
                    cornerStrokeColor: '#fff',
                    borderColor: color,
                    cornerSize: 10,
                    transparentCorners: false,
                    cornerStyle: 'circle',
                    padding: 4
                });

                group.goalId = item.id;
                canvas.add(group);
                canvas.setActiveObject(group);
                canvas.renderAll();
            }, { crossOrigin: 'anonymous' });
        } else {
            createPlaceholderCard(item, x, y, cardWidth, cardHeight, color, desc, yearText);
        }
    }

    // 이미지 없는 카드 생성
    function createPlaceholderCard(item, x, y, cardWidth, cardHeight, color, desc, yearText) {
        const bgRect = new fabric.Rect({
            width: cardWidth,
            height: cardHeight,
            rx: 12,
            ry: 12,
            fill: new fabric.Gradient({
                type: 'linear',
                coords: { x1: 0, y1: 0, x2: 0, y2: cardHeight },
                colorStops: [
                    { offset: 0, color: color + '33' },
                    { offset: 1, color: color + '88' }
                ]
            }),
            originX: 'center',
            originY: 'center',
            stroke: color,
            strokeWidth: 2
        });

        const icon = new fabric.Text('🎯', {
            fontSize: 36,
            originX: 'center',
            originY: 'center',
            top: -20
        });

        const descText = new fabric.Textbox(desc, {
            fontSize: 14,
            fontFamily: 'Noto Sans KR, sans-serif',
            fill: '#2D3047',
            fontWeight: '600',
            width: cardWidth - 16,
            originX: 'center',
            originY: 'center',
            top: 30,
            textAlign: 'center',
            splitByGrapheme: true
        });

        const yearBadge = new fabric.Text(yearText, {
            fontSize: 11,
            fontFamily: 'Noto Sans KR, sans-serif',
            fill: '#ffffff',
            fontWeight: '700',
            originX: 'center',
            originY: 'center',
            top: 60,
            backgroundColor: color,
            padding: 4
        });

        const group = new fabric.Group([bgRect, icon, descText, yearBadge], {
            left: x,
            top: y,
            originX: 'center',
            originY: 'center',
            cornerColor: color,
            cornerStrokeColor: '#fff',
            borderColor: color,
            cornerSize: 10,
            transparentCorners: false,
            cornerStyle: 'circle',
            padding: 4
        });

        group.goalId = item.id;
        canvas.add(group);
        canvas.setActiveObject(group);
        canvas.renderAll();
    }

    // ===== 목표 패널 클릭 이벤트 =====
    document.querySelectorAll('.goal-thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function() {
            const item = {
                id: this.dataset.id,
                description: this.dataset.description,
                year: this.dataset.year,
                category: this.dataset.category,
                image: this.dataset.image
            };

            // 이미 캔버스에 있는지 확인
            const exists = canvas.getObjects().some(obj => obj.goalId === item.id);
            if (exists) {
                // 이미 있으면 해당 객체 선택
                const existingObj = canvas.getObjects().find(obj => obj.goalId === item.id);
                canvas.setActiveObject(existingObj);
                canvas.renderAll();
                return;
            }

            // 랜덤 위치에 추가
            const padding = 100;
            const x = padding + Math.random() * (canvas.width - padding * 2);
            const y = padding + Math.random() * (canvas.height - padding * 2);
            addGoalToCanvas(item, x, y);
        });
    });

    // ===== 전체 추가 =====
    const addAllBtn = document.getElementById('add-all-btn');
    if (addAllBtn) {
        addAllBtn.addEventListener('click', function() {
            const thumbs = document.querySelectorAll('.goal-thumbnail');
            const total = thumbs.length;
            if (total === 0) return;

            // 기존 캔버스 초기화
            canvas.clear();
            canvas.backgroundColor = canvas.backgroundColor || '#FFFFFF';

            // 그리드 배치 계산
            const cols = Math.ceil(Math.sqrt(total));
            const rows = Math.ceil(total / cols);
            const cellW = canvas.width / cols;
            const cellH = canvas.height / rows;

            thumbs.forEach((thumb, i) => {
                const item = {
                    id: thumb.dataset.id,
                    description: thumb.dataset.description,
                    year: thumb.dataset.year,
                    category: thumb.dataset.category,
                    image: thumb.dataset.image
                };

                const col = i % cols;
                const row = Math.floor(i / cols);
                const x = cellW * col + cellW / 2;
                const y = cellH * row + cellH / 2;

                setTimeout(() => addGoalToCanvas(item, x, y), i * 100);
            });
        });
    }

    // ===== 배경 테마 =====
    const bgThemeBtn = document.getElementById('bg-theme-btn');
    const bgThemeDropdown = document.getElementById('bg-theme-dropdown');

    bgThemeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        bgThemeDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', () => {
        bgThemeDropdown.classList.add('hidden');
    });

    document.querySelectorAll('.bg-theme-option').forEach(opt => {
        opt.addEventListener('click', function() {
            const bg = this.dataset.bg;
            switch (bg) {
                case 'white':
                    canvas.backgroundColor = '#FFFFFF';
                    break;
                case 'dark':
                    canvas.backgroundColor = '#2D3047';
                    break;
                case 'cork':
                    canvas.backgroundColor = '#C4A882';
                    // 코르크 텍스처 효과 (패턴 시뮬레이션)
                    canvas.backgroundColor = new fabric.Gradient({
                        type: 'linear',
                        coords: { x1: 0, y1: 0, x2: canvas.width, y2: canvas.height },
                        colorStops: [
                            { offset: 0, color: '#D4B896' },
                            { offset: 0.3, color: '#C4A882' },
                            { offset: 0.6, color: '#D4B896' },
                            { offset: 1, color: '#B89B6E' }
                        ]
                    });
                    break;
                case 'gradient':
                    canvas.backgroundColor = new fabric.Gradient({
                        type: 'linear',
                        coords: { x1: 0, y1: 0, x2: canvas.width, y2: canvas.height },
                        colorStops: [
                            { offset: 0, color: '#E8F8F5' },
                            { offset: 0.5, color: '#FCE4EC' },
                            { offset: 1, color: '#E8F8F5' }
                        ]
                    });
                    break;
            }
            canvas.renderAll();
            bgThemeDropdown.classList.add('hidden');
        });
    });

    // ===== 텍스트 추가 =====
    document.getElementById('add-text-btn').addEventListener('click', function() {
        const text = new fabric.IText('나의 다짐을 적어보세요', {
            left: canvas.width / 2,
            top: canvas.height / 2,
            originX: 'center',
            originY: 'center',
            fontFamily: 'Noto Sans KR, sans-serif',
            fontSize: 20,
            fontWeight: '600',
            fill: '#2D3047',
            textAlign: 'center',
            cornerColor: '#4ECDC4',
            cornerStrokeColor: '#fff',
            borderColor: '#4ECDC4',
            cornerSize: 10,
            transparentCorners: false,
            cornerStyle: 'circle',
            padding: 8,
            editable: true
        });

        canvas.add(text);
        canvas.setActiveObject(text);
        text.enterEditing();
        text.selectAll();
        canvas.renderAll();
    });

    // ===== 되돌리기 =====
    document.getElementById('undo-btn').addEventListener('click', undo);

    // Ctrl+Z 키보드 단축키
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'z') {
            e.preventDefault();
            undo();
        }
        if (e.key === 'Delete' || e.key === 'Backspace') {
            const activeObj = canvas.getActiveObject();
            if (activeObj && !activeObj.isEditing) {
                e.preventDefault();
                canvas.remove(activeObj);
                canvas.discardActiveObject();
                canvas.renderAll();
            }
        }
    });

    // ===== 삭제 버튼 =====
    document.getElementById('delete-selected-btn').addEventListener('click', function() {
        const activeObj = canvas.getActiveObject();
        if (activeObj) {
            if (activeObj.type === 'activeSelection') {
                activeObj.forEachObject(obj => canvas.remove(obj));
                canvas.discardActiveObject();
            } else {
                canvas.remove(activeObj);
                canvas.discardActiveObject();
            }
            canvas.renderAll();
        }
    });

    // ===== 자동 배치 =====
    document.getElementById('auto-arrange-btn').addEventListener('click', function() {
        const objects = canvas.getObjects();
        if (objects.length === 0) return;

        const total = objects.length;
        const cols = Math.ceil(Math.sqrt(total));
        const padding = 20;
        const cellW = (canvas.width - padding * 2) / cols;
        const rows = Math.ceil(total / cols);
        const cellH = (canvas.height - padding * 2) / rows;

        objects.forEach((obj, i) => {
            const col = i % cols;
            const row = Math.floor(i / cols);
            const targetX = padding + cellW * col + cellW / 2;
            const targetY = padding + cellH * row + cellH / 2;

            // 부드러운 애니메이션
            obj.animate({
                left: targetX,
                top: targetY
            }, {
                duration: 400,
                easing: fabric.util.ease.easeOutCubic,
                onChange: canvas.renderAll.bind(canvas)
            });
        });
    });

    // ===== 이미지 저장 =====
    document.getElementById('download-btn').addEventListener('click', function() {
        // 선택 해제 후 깨끗한 이미지 생성
        canvas.discardActiveObject();
        canvas.renderAll();

        setTimeout(() => {
            const dataURL = canvas.toDataURL({
                format: 'png',
                quality: 1,
                multiplier: 2
            });

            const link = document.createElement('a');
            link.download = 'my-vision-board.png';
            link.href = dataURL;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }, 100);
    });

    // ===== 줌 컨트롤 =====
    let currentZoom = 1;
    const ZOOM_STEP = 0.1;
    const ZOOM_MIN = 0.3;
    const ZOOM_MAX = 2;

    function applyZoom(zoom) {
        currentZoom = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, zoom));
        canvasInner.style.transform = `scale(${currentZoom})`;
        canvasInner.style.width = (CANVAS_W * currentZoom) + 'px';
        canvasInner.style.height = (CANVAS_H * currentZoom) + 'px';
        document.getElementById('zoom-level').textContent = Math.round(currentZoom * 100) + '%';
    }

    function zoomToFit() {
        const wrapperW = canvasWrapper.clientWidth;
        const fitZoom = Math.min(wrapperW / CANVAS_W, 1);
        applyZoom(fitZoom);
    }

    document.getElementById('zoom-in-btn').addEventListener('click', () => applyZoom(currentZoom + ZOOM_STEP));
    document.getElementById('zoom-out-btn').addEventListener('click', () => applyZoom(currentZoom - ZOOM_STEP));
    document.getElementById('zoom-fit-btn').addEventListener('click', zoomToFit);

    // 초기 로드 시 화면에 맞추기
    zoomToFit();

    // 창 크기 변경 시 자동 줌 조정
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(zoomToFit, 200);
    });
});
</script>

<style>
    /* 캔버스 컨테이너 */
    #canvas-wrapper {
        background: #f9fafb;
    }

    #canvas-wrapper canvas {
        display: block !important;
    }

    /* 목표 패널 스크롤 */
    #goals-panel {
        scroll-snap-type: x mandatory;
    }

    #goals-panel .goal-thumbnail {
        scroll-snap-align: start;
    }

    /* 툴바 드롭다운 애니메이션 */
    #bg-theme-dropdown {
        animation: dropdownFade 0.15s ease-out;
    }

    @keyframes dropdownFade {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 캔버스 선택 컨트롤 커스터마이징 */
    .canvas-container {
        border-radius: 0 !important;
    }

    /* 모바일 터치 최적화 */
    @media (max-width: 767px) {
        .upper-canvas {
            touch-action: none;
        }
    }

    /* 캔버스 래퍼 스크롤바 */
    #canvas-wrapper::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    #canvas-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    #canvas-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    #canvas-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* 캔버스 inner 트랜지션 */
    #canvas-inner {
        transition: transform 0.15s ease-out;
    }
</style>
@endsection
