@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.9); }
    }
    @keyframes popIn {
        from { transform: scale(0.5); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    .fade-in { animation: fadeIn 0.4s forwards; }
    .fade-out { animation: fadeOut 0.3s forwards; }
    .pop-in { animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .bounce-hover:hover { animation: bounce 0.6s; }

    .life-map-btn {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .life-map-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .life-map-btn:hover::before {
        left: 100%;
    }

    #chart {
        min-height: 600px !important;
        height: 600px !important;
    }

    /* Range Slider 스타일링 */
    .range-slider {
        -webkit-appearance: none;
        appearance: none;
        height: 8px;
        border-radius: 5px;
        background: linear-gradient(to right, #ef4444 0%, #f59e0b 25%, #10b981 50%, #3b82f6 75%, #10b981 100%);
        outline: none;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .range-slider:hover {
        opacity: 1;
    }

    .range-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #eba568;
        cursor: pointer;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
    }

    .range-slider::-webkit-slider-thumb:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .range-slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #eba568;
        cursor: pointer;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
    }

    .range-slider::-moz-range-thumb:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
</style>

<div class="container mx-auto px-4 py-8">
    <!-- 페이지 헤더 -->
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">나의 인생 지도 그리기</h1>
        <p class="text-lg text-gray-600">인생의 순간들을 차트로 기록하고 나만의 성장 스토리를 만들어보세요.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_420px] lg:gap-8">
        <!-- 차트 영역 -->
        <div class="order-2 lg:order-1">
            <div id="chart" class="w-full min-h-[600px] h-full border border-gray-200 rounded-lg bg-white shadow-lg hover:shadow-xl transition-all duration-200 pop-in"></div>
        </div>

        <!-- 컨트롤 패널 -->
        <div class="order-1 lg:order-2 space-y-6">
            <!-- 기억 추가 섹션 -->
            <div class="p-6 border border-gray-200 rounded-lg bg-white shadow-lg hover:shadow-xl transition-all duration-200 pop-in">
                <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 bg-point1 rounded-full"></span>
                    기억 추가하기
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="age" class="block font-medium mb-2 text-gray-700">몇 살 때의 기억인가요?</label>
                        <input type="number" id="age" placeholder="예: 7살이면 7 입력" min="0" max="99"
                               class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 text-base transition-all duration-200 focus:outline-none focus:border-point1 focus:bg-white focus:ring-2 focus:ring-point1/20">
                    </div>

                    <div>
                        <label for="scoreRange" class="block font-medium mb-2 text-gray-700">기분은 어땠나요? (슬퍼요 -10 ↔ 행복해요 10)</label>
                        <input type="range" id="scoreRange" min="-10" max="10" step="1" value="0"
                               class="w-full range-slider">
                        <div id="scoreDisplay" class="mt-2 font-medium text-center text-lg">
                            지금 기분: <span id="scoreValue" class="text-point1 font-bold">0</span>
                            <span id="moodEmoji" class="ml-2">😐</span>
                        </div>
                    </div>

                    <div>
                        <label for="label" class="block font-medium mb-2 text-gray-700">어떤 일이 있었나요? (한 줄로 써주세요)</label>
                        <input type="text" id="label" placeholder="예: 친구들과 놀이터에서 놀았어요" maxlength="50"
                               class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-800 text-base transition-all duration-200 focus:outline-none focus:border-point1 focus:bg-white focus:ring-2 focus:ring-point1/20">
                    </div>

                    <div id="errorMessage" class="text-point3 text-sm mt-4 min-h-[1.2em] font-medium"></div>

                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <button id="addBtn" class="life-map-btn px-4 py-3 bg-point1 hover:bg-point1/90 text-white rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                            <span>➕</span>추가하기
                        </button>
                        <button id="updateBtn" class="life-map-btn px-4 py-3 bg-point2 hover:bg-point2/90 text-white rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed" disabled>
                            <span>✏️</span>수정하기
                        </button>
                        <button id="deleteBtn" class="life-map-btn px-4 py-3 bg-point3 hover:bg-point3/90 text-white rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed" disabled>
                            <span>🗑️</span>지우기
                        </button>
                        <button id="resetBtn" class="life-map-btn px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold transition-all duration-300 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                            <span>🔄</span>다시 시작
                        </button>
                    </div>
                </div>
            </div>

            <!-- 기억 목록 섹션 -->
            <div class="p-6 border border-gray-200 rounded-lg bg-white shadow-lg hover:shadow-xl transition-all duration-200 pop-in">
                <h2 class="text-xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-2 bg-point1 rounded-full"></span>
                    나의 기억 목록
                </h2>

                <div class="max-h-[300px] overflow-y-auto border border-gray-200 rounded-lg">
                    <table id="dataTable" class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="p-3 text-center border-b border-gray-200 sticky top-0 font-medium">
                                    <input type="checkbox" id="selectAll" title="전체 선택" class="w-4 h-4 text-point1 border-gray-300 rounded focus:ring-point1">
                                </th>
                                <th class="p-3 text-center border-b border-gray-200 sticky top-0 font-medium text-gray-700">나이</th>
                                <th class="p-3 text-center border-b border-gray-200 sticky top-0 font-medium text-gray-700">기분</th>
                                <th class="p-3 text-center border-b border-gray-200 sticky top-0 font-medium text-gray-700">무슨 일이 있었나요?</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // --- DOM 요소 ---
    const chartDom = document.getElementById("chart");
    const ageInput = document.getElementById("age");
    const scoreRange = document.getElementById("scoreRange");
    const scoreValue = document.getElementById("scoreValue");
    const labelInput = document.getElementById("label");
    const addBtn = document.getElementById("addBtn");
    const updateBtn = document.getElementById("updateBtn");
    const deleteBtn = document.getElementById("deleteBtn");
    const resetBtn = document.getElementById("resetBtn");
    const dataTableBody = document.querySelector("#dataTable tbody");
    const selectAllCheckbox = document.getElementById("selectAll");
    const errorMessage = document.getElementById("errorMessage");

    // --- 상태 변수 ---
    let chartInstance;
    let lifeData = [];
    let originalDataBeforeDrag = null;

    // --- 초기화 ---
    function initialize() {
        // 차트 컨테이너 높이 설정
        chartDom.style.height = '600px';
        chartDom.style.width = '100%';

        chartInstance = echarts.init(chartDom);
        chartInstance.setOption(getChartOption(true));
        chartInstance.on("graphic", "dragstart", onPointDragStart);
        chartInstance.on("graphic", "drag", onPointDrag);
        chartInstance.on("graphic", "dragend", onPointDragEnd);

        setupEventListeners();
        refreshAll();
    }

    function setupEventListeners() {
        addBtn.addEventListener("click", addDataPoint);
        updateBtn.addEventListener("click", updateDataPoint);
        deleteBtn.addEventListener("click", deleteDataPoints);
        resetBtn.addEventListener("click", resetAll);
        selectAllCheckbox.addEventListener("change", toggleSelectAll);

        if (scoreRange && scoreValue) {
            const moodEmoji = document.getElementById("moodEmoji");
            scoreRange.addEventListener("input", () => {
                const score = parseInt(scoreRange.value, 10);
                scoreValue.textContent = score;

                // 기분에 따른 이모지 변경
                if (score >= 8) moodEmoji.textContent = "🤩"; // 매우 행복
                else if (score >= 5) moodEmoji.textContent = "😊"; // 행복
                else if (score >= 2) moodEmoji.textContent = "🙂"; // 좋음
                else if (score >= -1) moodEmoji.textContent = "😐"; // 보통
                else if (score >= -4) moodEmoji.textContent = "😟"; // 별로
                else if (score >= -7) moodEmoji.textContent = "😢"; // 슬픔
                else moodEmoji.textContent = "😭"; // 매우 슬픔

                // 색상도 변경
                if (score > 0) {
                    scoreValue.className = "text-green-600 font-bold";
                } else if (score < 0) {
                    scoreValue.className = "text-point3 font-bold";
                } else {
                    scoreValue.className = "text-point1 font-bold";
                }
            });
        }

        window.addEventListener("resize", () => {
            if (chartInstance) {
                // 차트 컨테이너 크기 재설정
                chartDom.style.height = '600px';
                chartDom.style.width = '100%';
                chartInstance.resize();
            }
        });

        // 나이 입력 2자리 제한
        ageInput.addEventListener("input", (e) => {
            if (e.target.value.length > 2) {
                e.target.value = e.target.value.slice(0, 2);
            }
        });
    }

    // --- 차트 옵션 ---
    function getChartOption(isInitial = false) {
        const textColor = "#374151";
        const axisLineColor = "#d1d5db";
        const splitLineColor = "rgba(0, 0, 0, 0.05)";
        const zeroLineColor = "#6b7280";

        const chartData = [...lifeData];

        const hasZeroPoint = chartData.some(item => item.age === 0);
        if (!hasZeroPoint) {
            chartData.unshift({ id: -1, age: 0, score: 0, label: "시작점" });
        }

        chartData.sort((a, b) => a.age - b.age);

        const formattedData = chartData.map(item => [item.age, item.score, item.id, item.label]);

        return {
            animation: !isInitial,
            animationEasing: "elasticOut",
            animationDuration: 1000,
            tooltip: {
                trigger: 'item',
                triggerOn: 'mousemove|click',
                enterable: true,
                formatter: function(params) {
                    const age = params.value[0];
                    const score = params.value[1];
                    const label = params.value[3] || '';

                    let emoji = '😐';
                    if (score >= 8) emoji = '🤩';
                    else if (score >= 5) emoji = '😊';
                    else if (score >= 2) emoji = '🙂';
                    else if (score >= -1) emoji = '😐';
                    else if (score >= -4) emoji = '😟';
                    else if (score >= -7) emoji = '😢';
                    else emoji = '😭';

                    return `
                        <div style="padding: 8px; font-size: 14px;">
                            <div style="font-weight: bold; margin-bottom: 4px;">
                                ${age}세 ${emoji}
                            </div>
                            <div style="margin-bottom: 4px;">
                                😊 기분: ${score}
                            </div>
                            ${label ? `<div>📝 ${label}</div>` : ''}
                        </div>
                    `;
                },
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                borderColor: '#eba568',
                borderWidth: 2,
                textStyle: {
                    color: '#ffffff',
                    fontSize: 13
                }
            },
            grid: {
                left: "3%",
                right: "4%",
                bottom: "3%",
                containLabel: true
            },
            xAxis: {
                type: "value",
                name: "나이",
                min: 0,
                axisLabel: {
                    color: textColor,
                    formatter: function (value) {
                        if (Math.floor(value) === value) {
                            return value;
                        }
                        return '';
                    }
                },
                axisLine: { lineStyle: { color: axisLineColor } },
                splitLine: { show: true, lineStyle: { color: splitLineColor } }
            },
            yAxis: {
                type: "value",
                name: "행복도",
                min: -10,
                max: 10,
                interval: 2,
                axisLabel: { color: textColor },
                axisLine: { lineStyle: { color: axisLineColor } },
                splitLine: { show: true, lineStyle: { color: splitLineColor } }
            },
            series: [{
                id: "life-line",
                type: "line",
                data: formattedData,
                symbol: "circle",
                symbolSize: 12,
                smooth: 0.4,
                showSymbol: true,
                hoverAnimation: true,
                lineStyle: { width: 3 },
                itemStyle: {
                    color: params => {
                        const score = params.value[1];
                        if (score >= 5) return "#10b981"; // 초록 (행복)
                        else if (score >= 0) return "#3b82f6"; // 파랑 (보통)
                        else if (score >= -5) return "#f59e0b"; // 주황 (별로)
                        else return "#ef4444"; // 빨강 (슬픔)
                    },
                    borderWidth: 2
                },
                label: {
                    show: true,
                    position: 'top',
                    formatter: function(params) {
                        const age = params.value[0];
                        const score = params.value[1];
                        const label = params.value[3] || '';

                        // 점수에 따른 이모지 선택
                        let emoji = '😐';
                        if (score >= 8) emoji = '🤩';
                        else if (score >= 5) emoji = '😊';
                        else if (score >= 2) emoji = '🙂';
                        else if (score >= -1) emoji = '😐';
                        else if (score >= -4) emoji = '😟';
                        else if (score >= -7) emoji = '😢';
                        else emoji = '😭';

                        // 10자마다 줄바꿈 처리
                        const formatLabel = (text) => {
                            if (!text) return '';
                            const lines = [];
                            for (let i = 0; i < text.length; i += 10) {
                                lines.push(text.slice(i, i + 10));
                            }
                            return lines.join('\n');
                        };

                        return `${age}세 ${emoji}\n${formatLabel(label)}`;
                    },
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    borderColor: '#eba568',
                    borderWidth: 2,
                    borderRadius: 6,
                    padding: [6, 8],
                    color: '#374151',
                    fontSize: 12,
                    fontWeight: 'bold',
                    textAlign: 'center'
                },
                emphasis: {
                    focus: "series",
                    itemStyle: {
                        borderColor: "#eba568",
                        borderWidth: 5,
                        shadowBlur: 20,
                        shadowColor: "rgba(235, 165, 104, 0.6)"
                    },
                    scale: 1.2
                },
                markLine: {
                    silent: true,
                    data: [{ yAxis: 0 }],
                    lineStyle: { color: zeroLineColor, width: 2 }
                }
            }],
            graphic: []
        };
    }

    // --- 데이터 관리 ---
    function addDataPoint() {
        if (!validateForm()) {
            return;
        }
        const age = parseInt(ageInput.value, 10);
        const score = parseInt(scoreRange.value, 10);
        const label = labelInput.value.trim();

        if (age === 0) {
            errorMessage.textContent = "😅 0세는 시작점이라 추가할 수 없어요!";
            return;
        }

        const existingIndex = lifeData.findIndex(d => d.age === age);

        if (existingIndex > -1) {
            if (!confirm(`🤔 '${age}세'에 이미 기억이 있어요! 덮어쓰시겠어요?`)) return;
            lifeData[existingIndex] = { ...lifeData[existingIndex], score, label };
        } else {
            const newItem = { id: Date.now(), age, score, label };
            lifeData.push(newItem);
        }
        sortData();
        refreshAll();
        clearForm();
    }

    function updateDataPoint() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length !== 1 || !validateForm()) return;

        const idToUpdate = selectedIds[0];
        const dataIndex = lifeData.findIndex(d => d.id === idToUpdate);
        const age = parseInt(ageInput.value, 10);
        const score = parseInt(scoreRange.value, 10);
        const label = labelInput.value.trim();

        if (age === 0) {
            errorMessage.textContent = "😅 0세는 시작점이라 수정할 수 없어요!";
            return;
        }

        const existingIndex = lifeData.findIndex(d => d.age === age && d.id !== idToUpdate);
        if (existingIndex > -1) {
            if (!confirm(`🤔 '${age}세'에 다른 기억이 있어요! 덮어쓰고 전에 있던 기억은 지울까요?`)) return;
            lifeData.splice(existingIndex, 1);
        }

        lifeData[dataIndex] = { id: idToUpdate, age, score, label };
        sortData();
        refreshAll();
        clearForm();
    }

    function deleteDataPoints() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) return;

        if (!confirm(`🤔 정말로 ${selectedIds.length}개의 기억을 지울까요?`)) return;

        const rowsToDelete = Array.from(dataTableBody.querySelectorAll("input:checked")).map(cb => cb.closest("tr"));
        rowsToDelete.forEach(row => row.classList.add("fade-out"));

        setTimeout(() => {
            lifeData = lifeData.filter(d => !selectedIds.includes(d.id));
            refreshAll();
            clearForm();
        }, 300);
    }

    function resetAll() {
        if (!confirm("🤔 정말로 모든 기억을 처음부터 다시 시작하시겠어요?")) return;
        lifeData = [];
        refreshAll();
        clearForm();
    }

    function sortData() {
        lifeData.sort((a, b) => a.age - b.age);
    }

    // --- UI 갱신 ---
    function refreshAll() {
        updateChart();
        updateTable();
        updateButtonStates();
    }

    function updateChart() {
        if (chartInstance) {
            // 차트 컨테이너 크기 확인 및 설정
            chartDom.style.height = '600px';
            chartDom.style.width = '100%';

            const option = getChartOption();
            chartInstance.setOption(option, { notMerge: true });

            const chartDataWithZeroPoint = [...lifeData];
            if (!chartDataWithZeroPoint.some(item => item.age === 0)) {
                chartDataWithZeroPoint.unshift({ id: -1, age: 0, score: 0, label: "시작점" });
            }
            chartDataWithZeroPoint.sort((a, b) => a.age - b.age);
            const formattedDataForGraphic = chartDataWithZeroPoint.map(item => [item.age, item.score, item.id, item.label]);

            const graphicElements = echarts.util.map(formattedDataForGraphic.filter(item => item[2] !== -1), (item) => {
                if (!chartInstance.isDisposed() && chartInstance.getWidth() > 0 && chartInstance.getHeight() > 0) {
                    const position = chartInstance.convertToPixel('grid', [item[0], item[1]]);

                    return {
                        type: 'circle',
                        id: `drag-${item[2]}`,
                        position: position,
                        shape: { r: 8 },
                        invisible: true,
                        draggable: true,
                        z: 50
                    };
                } else {
                    return null;
                }
            }).filter(Boolean);

            // 드래그 요소 활성화
            chartInstance.setOption({ graphic: graphicElements });
        }
    }

    function updateTable() {
        dataTableBody.innerHTML = "";
        lifeData.forEach(data => {
            const row = document.createElement("tr");
            row.dataset.id = data.id;
            row.classList.add("fade-in", "transition-colors", "duration-200", "hover:bg-gray-50");

            row.innerHTML = `
                <td class="p-3 text-center border-b border-gray-200 last:border-b-0">
                    <input type="checkbox" data-id="${data.id}" class="w-4 h-4 text-point1 border-gray-300 rounded focus:ring-point1">
                </td>
                <td class="p-3 text-center border-b border-gray-200 last:border-b-0 text-gray-700">${data.age}</td>
                <td class="p-3 text-center border-b border-gray-200 last:border-b-0 text-gray-700">${data.score}</td>
                <td class="p-3 text-center border-b border-gray-200 last:border-b-0 text-gray-700" title="${data.label}">${data.label}</td>
            `;

            row.querySelector("input").addEventListener("change", handleCheckboxChange);
            dataTableBody.appendChild(row);
        });
    }

    function clearForm() {
        ageInput.value = "";
        if (scoreRange) scoreRange.value = 0;
        if (scoreValue) scoreValue.textContent = 0;
        labelInput.value = "";
        errorMessage.textContent = "";
        Array.from(dataTableBody.querySelectorAll("input:checked")).forEach(cb => cb.checked = false);
        selectAllCheckbox.checked = false;
        updateButtonStates();
    }

    function updateButtonStates() {
        const selectedCount = getSelectedIds().length;
        updateBtn.disabled = selectedCount !== 1;
        deleteBtn.disabled = selectedCount === 0;
        if (selectedCount === 1) {
            const selectedId = getSelectedIds()[0];
            const data = lifeData.find(d => d.id === selectedId);
            if (data) {
                ageInput.value = data.age;
                if (scoreRange) scoreRange.value = data.score;
                if (scoreValue) scoreValue.textContent = data.score;
                labelInput.value = data.label;
            }
        }
    }

    // --- 이벤트 핸들러 ---
    function handleCheckboxChange() {
        const selectedCount = getSelectedIds().length;
        const totalCount = lifeData.length;
        selectAllCheckbox.checked = selectedCount === totalCount && totalCount > 0;
        updateButtonStates();
    }

    function toggleSelectAll(e) {
        const isChecked = e.target.checked;
        dataTableBody.querySelectorAll("input[type='checkbox']:not([disabled])").forEach(cb => cb.checked = isChecked);
        updateButtonStates();
    }

    function getSelectedIds() {
        return Array.from(dataTableBody.querySelectorAll("input:checked"))
            .map(cb => parseInt(cb.dataset.id, 10));
    }

    function validateForm() {
        const age = ageInput.value, score = scoreRange ? scoreRange.value : "";
        if (age === "" || score === "") {
            errorMessage.textContent = "😅 나이와 기분을 꼭 입력해주세요!";
            return false;
        }
        const ageNum = parseInt(age, 10);
        if (isNaN(ageNum) || ageNum < 0 || ageNum > 120) {
            errorMessage.textContent = "😅 나이는 0살부터 120살 사이로 써주세요!";
            return false;
        }
        const scoreNum = parseInt(score, 10);
        if (isNaN(scoreNum) || scoreNum < -10 || scoreNum > 10) {
            errorMessage.textContent = "😅 기분은 -10부터 10 사이로 선택해주세요!";
            return false;
        }
        errorMessage.textContent = "";
        return true;
    }

    // --- 드래그 핸들러 ---
    function onPointDragStart(params) {
        const id = params.topTarget.id.split("-")[1];
        const dataIndex = lifeData.findIndex(d => d.id == id);
        originalDataBeforeDrag = { ...lifeData[dataIndex] };
    }

    function onPointDrag(params) {
        const id = params.topTarget.id.split("-")[1];
        const dataIndex = lifeData.findIndex(d => d.id == id);
        const pointInGrid = chartInstance.convertFromPixel("grid", this.position);
        lifeData[dataIndex].age = Math.max(0, Math.min(120, Math.round(pointInGrid[0])));
        lifeData[dataIndex].score = Math.max(-10, Math.min(10, Math.round(pointInGrid[1])));
        chartInstance.dispatchAction({ type: "updateAxisPointer", seriesId: "life-line" });
    }

    function onPointDragEnd(params) {
        const id = params.topTarget.id.split("-")[1];
        const draggedIndex = lifeData.findIndex(d => d.id == id);
        const draggedData = lifeData[draggedIndex];
        const existingIndex = lifeData.findIndex(d => d.age === draggedData.age && d.id !== draggedData.id);

        if (existingIndex > -1) {
            if (confirm(`🤔 '${draggedData.age}세'에 다른 기억이 있어요! 덮어쓰고 전에 있던 기억은 지울까요?`)) {
                lifeData.splice(existingIndex, 1);
            } else {
                lifeData[draggedIndex] = originalDataBeforeDrag; // 드래그 취소
            }
        }
        sortData();
        refreshAll();
        clearForm();
    }

    // --- 앱 시작 ---
    setTimeout(() => {
        initialize();
    }, 0);
});
</script>

@endsection