@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="svg-container">
        <svg id="hexagonSvg" viewBox="0 0 1024 1024" preserveAspectRatio="xMidYMid meet">
            <defs>
                <polygon id="hexagon" points="
                    -50,0
                    -25,-43.3
                    25,-43.3
                    50,0
                    25,43.3
                    -25,43.3
                " />
            </defs>

            <g fill="none" stroke="black" stroke-width="2" id="haxogon_center_cluster" class="hexagon_cluster" transform="translate(512,512) scale(3)">
                <use xlink:href="#hexagon" x="0" y="0" id="hexagon_center_center"/>
                <text class="hexagon-text" x="0" y="0"></text>
                
                <use xlink:href="#hexagon" x="0" y="-86.6" id="hexagon_center_top"/>
                <text class="hexagon-text" x="0" y="-86.6"></text>
                
                <use xlink:href="#hexagon" x="75" y="-43.3" id="hexagon_center_top-right"/>
                <text class="hexagon-text" x="75" y="-43.3"></text>
                
                <use xlink:href="#hexagon" x="75" y="43.3" id="hexagon_center_bottom-right"/>
                <text class="hexagon-text" x="75" y="43.3"></text>
                
                <use xlink:href="#hexagon" x="0" y="86.6" id="hexagon_center_bottom"/>
                <text class="hexagon-text" x="0" y="86.6"></text>
                
                <use xlink:href="#hexagon" x="-75" y="43.3" id="hexagon_center_bottom-left"/>
                <text class="hexagon-text" x="-75" y="43.3"></text>
                
                <use xlink:href="#hexagon" x="-75" y="-43.3" id="hexagon_center_top-left"/>
                <text class="hexagon-text" x="-75" y="-43.3"></text>
            </g>
        </svg>
    </div>
    <div class="control-buttons">
        <button id="btn_edit_mode" class="px-6 py-2 rounded-lg bg-point text-point hover:bg-opacity-90 transition-all">편집</button>
        <button id="btn_input_content" class="px-6 py-2 rounded-lg bg-point text-point hover:bg-opacity-90 transition-all hidden">내용 입력</button>
        <button id="btn_add_cube" class="px-6 py-2 rounded-lg bg-point text-point hover:bg-opacity-90 transition-all hidden">추가</button>
        <button id="btn_save" class="px-6 py-2 rounded-lg bg-point text-point hover:bg-opacity-90 transition-all hidden">저장</button>
    </div>
</div>

<!-- 모달 -->
<div class="modal fade" id="textModal" tabindex="-1" aria-labelledby="textModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-lg font-bold" id="textModalLabel">내용을 입력해주세요</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span class="text-2xl">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="hexagonText" placeholder="내용을 입력하세요">
            </div>
            <div class="modal-footer">
                <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition-colors" data-bs-dismiss="modal">닫기</button>
                <button type="button" class="px-4 py-2 rounded-lg bg-point text-point hover:bg-opacity-90 transition-colors" id="confirmBtn">확인</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .svg-container {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        height: calc(100vh - 120px);
        background: transparent;
    }

    svg {
        width: 100%;
        height: 100%;
        max-width: 100vmin;
        max-height: 100%;
        background-color: transparent;
    }

    .hexagon_cluster use {
        stroke: #34383d;
        stroke-width: 1.2;
        transition: all 0.3s ease;
        fill: rgba(244, 225, 118, 0.1);
    }

    .svg-container.edit-mode .hexagon_cluster use:hover {
        fill: rgba(244, 225, 118, 0.3);
        stroke: #34383d;
        stroke-width: 2;
        filter: drop-shadow(0 2px 4px rgba(52, 56, 61, 0.1));
    }

    .selected {
        fill: rgba(244, 225, 118, 0.4) !important;
        stroke-width: 2 !important;
        stroke: #34383d !important;
        filter: drop-shadow(0 2px 4px rgba(52, 56, 61, 0.3));
    }

    .hexagon-text {
        font-family: 'Noto Sans KR', sans-serif;
        font-size: 14px;
        font-weight: 500;
        fill: #34383d;
        text-anchor: middle;
        dominant-baseline: middle;
        pointer-events: none;
        user-select: none;
    }

    .control-buttons {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        display: flex;
        gap: 15px;
    }

    @media (max-width: 768px) {
        .svg-container {
            height: 80vh;
            padding: 10px;
        }

        .control-buttons {
            bottom: 20px;
        }

        .control-buttons button {
            @apply px-4 py-2 text-sm;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let selectedHexagon;
let editModeActive = false;
const modal = new bootstrap.Modal(document.getElementById('textModal'));

// 기존 데이터 로드
@if($beecube && $beecube->content)
    const savedContent = @json($beecube->content);
    loadSavedContent(savedContent);
@endif

function loadSavedContent(content) {
    Object.entries(content).forEach(([id, text]) => {
        const textElement = document.querySelector(`#${id} + text.hexagon-text`);
        if (textElement) {
            textElement.textContent = text;
        }
    });
}

// 편집 모드 토글 버튼
$('#btn_edit_mode').on('click', function() {
    editModeActive = !editModeActive;
    $(this).text(editModeActive ? '편집 완료' : '편집');
    $('.svg-container').toggleClass('edit-mode');
    $('#btn_save').toggle(editModeActive);
    
    if (!editModeActive) {
        $('#btn_input_content').hide();
        $('#btn_add_cube').hide();
        $('.selected').removeClass('selected');
    }
});

// 육각형 클릭 이벤트
$('#hexagonSvg').on('click', 'use', function(event) {
    if (editModeActive) {
        $('.selected').removeClass('selected');
        selectedHexagon = event.target;
        $(selectedHexagon).addClass('selected');
        $('#btn_input_content').show();

        if ($(selectedHexagon).parent().attr('id') == 'haxogon_center_cluster') {
            if (!$(selectedHexagon).attr('id').split('_')[2].includes('center')) {
                $('#btn_add_cube').show();
            } else {
                $('#btn_add_cube').hide();
            }
        } else {
            $('#btn_add_cube').hide();
        }
    }
});

// 내용 입력 버튼
$('#btn_input_content').on('click', function() {
    const existingText = $(selectedHexagon).next('text.hexagon-text').text();
    $('#hexagonText').val(existingText);
    modal.show();
});

// 모달 확인 버튼
$('#confirmBtn').on('click', function() {
    const text = $('#hexagonText').val().trim();
    if (text) {
        $(selectedHexagon).next('text.hexagon-text').remove();

        const textElement = document.createElementNS("http://www.w3.org/2000/svg", "text");
        textElement.setAttribute('class', 'hexagon-text');
        textElement.textContent = text;

        const hexagonBBox = selectedHexagon.getBBox();
        textElement.setAttribute('x', hexagonBBox.x + hexagonBBox.width / 2);
        textElement.setAttribute('y', hexagonBBox.y + hexagonBBox.height / 2);

        const fontSize = Math.min(12, 80 / text.length);
        textElement.style.fontSize = `${fontSize}px`;

        selectedHexagon.parentNode.insertBefore(textElement, selectedHexagon.nextSibling);
        modal.hide();
        
        $('#btn_input_content').hide();
        $('#btn_add_cube').hide();
        $('.selected').removeClass('selected');
    }
});

// 저장 버튼
$('#btn_save').on('click', function() {
    const content = {};
    document.querySelectorAll('.hexagon_cluster use').forEach(hexagon => {
        const text = $(hexagon).next('text.hexagon-text').text();
        if (text) {
            content[hexagon.id] = text;
        }
    });

    $.ajax({
        url: '{{ route("beecube.save") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            content: content
        },
        success: function(response) {
            if (response.success) {
                alert('저장되었습니다.');
            } else {
                alert('저장에 실패했습니다.');
            }
        },
        error: function() {
            alert('저장에 실패했습니다.');
        }
    });
});

// 추가 버튼 및 클러스터 추가 기능
$('#btn_add_cube').on('click', function() {
    if (selectedHexagon) {
        const selectedId = $(selectedHexagon).attr('id');
        const parts = selectedId.split('_');
        const direction = parts[parts.length - 1];
        addNewCluster(direction);
    }
});

function addNewCluster(direction) {
    const sideLength = 50 * 3;
    const hexHeight = Math.sqrt(3) * sideLength;
    const hexWidth = 2 * sideLength;
    const clusterSizeX = 1.5 * hexWidth;
    const clusterSizeY = hexHeight + (hexHeight / 2);

    let newX = 0, newY = 0;
    const parentCluster = $(selectedHexagon).closest('g');
    const parentTransform = parentCluster.attr('transform');
    const parentTranslate = parentTransform.match(/translate\(([-\d.]+),\s*([-\d.]+)\)/);
    const parentX = parseFloat(parentTranslate[1]);
    const parentY = parseFloat(parentTranslate[2]);

    const spacing = 2.1;

    switch(direction) {
        case 'top':
            newX = parentX;
            newY = parentY - clusterSizeY * spacing;
            break;
        case 'top-right':
            newX = parentX + clusterSizeX * spacing * 0.75;
            newY = parentY - clusterSizeY * spacing * 0.5;
            break;
        case 'bottom-right':
            newX = parentX + clusterSizeX * spacing * 0.75;
            newY = parentY + clusterSizeY * spacing * 0.5;
            break;
        case 'bottom':
            newX = parentX;
            newY = parentY + clusterSizeY * spacing;
            break;
        case 'bottom-left':
            newX = parentX - clusterSizeX * spacing * 0.75;
            newY = parentY + clusterSizeY * spacing * 0.5;
            break;
        case 'top-left':
            newX = parentX - clusterSizeX * spacing * 0.75;
            newY = parentY - clusterSizeY * spacing * 0.5;
            break;
        default:
            return;
    }

    const newClusterId = `haxogon_${direction}_cluster`;
    const existingCluster = document.getElementById(newClusterId);

    if (existingCluster) {
        const confirmDelete = confirm("기존 클러스터가 초기화됩니다. 계속 진행하시겠습니까?");
        if (confirmDelete) {
            existingCluster.remove();
        } else {
            return;
        }
    }

    const newCluster = document.createElementNS("http://www.w3.org/2000/svg", 'g');
    newCluster.setAttribute('fill', 'none');
    newCluster.setAttribute('stroke', 'gray');
    newCluster.setAttribute('stroke-width', '2');
    newCluster.setAttribute('id', newClusterId);
    newCluster.setAttribute('class', 'hexagon_cluster');
    newCluster.setAttribute('transform', `translate(${newX}, ${newY}) scale(3)`);

    const positions = [
        { x: 0, y: 0, idSuffix: '_center' },
        { x: 0, y: -86.6, idSuffix: '_top' },
        { x: 75, y: -43.3, idSuffix: '_top-right' },
        { x: 75, y: 43.3, idSuffix: '_bottom-right' },
        { x: 0, y: 86.6, idSuffix: '_bottom' },
        { x: -75, y: 43.3, idSuffix: '_bottom-left' },
        { x: -75, y: -43.3, idSuffix: '_top-left' },
    ];

    positions.forEach(pos => {
        const use = document.createElementNS("http://www.w3.org/2000/svg", 'use');
        use.setAttributeNS("http://www.w3.org/1999/xlink", 'xlink:href', '#hexagon');
        use.setAttribute('x', pos.x);
        use.setAttribute('y', pos.y);
        use.setAttribute('id', `haxogon_${direction}${pos.idSuffix}`);
        newCluster.appendChild(use);

        const text = document.createElementNS("http://www.w3.org/2000/svg", 'text');
        text.setAttribute('class', 'hexagon-text');
        text.setAttribute('x', pos.x);
        text.setAttribute('y', pos.y);
        newCluster.appendChild(text);
    });

    document.getElementById('hexagonSvg').appendChild(newCluster);
}
</script>
@endpush
@endsection 