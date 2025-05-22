// 전역 로딩 관리자
const LoadingManager = {
    init() { 
        // 로딩 컨테이너가 없으면 생성
        if (!document.getElementById('globalLoadingContainer')) {
            const loadingHTML = `
                <div id="globalLoadingContainer" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
                    <div class="bg-white rounded-lg p-4">
                        <svg class="animate-spin h-10 w-10 text-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', loadingHTML);
        }
    },

    show() {
        this.init();
        const loader = document.getElementById('globalLoadingContainer');
        loader.classList.remove('hidden');
        loader.classList.add('flex');
        document.body.style.overflow = 'hidden';
    },

    hide() {
        const loader = document.getElementById('globalLoadingContainer');
        if (loader) {
            loader.classList.add('hidden');
            loader.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }
};

// 전역으로 사용할 수 있도록 window 객체에 추가
window.LoadingManager = LoadingManager;

/**
 * 숫자에 천단위 콤마를 추가하는 함수
 * @param {number|string} x - 포맷팅할 숫자 또는 문자열
 * @returns {string} 천단위 콤마가 포함된 문자열
 */
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * 입력 필드에 천단위 콤마를 적용하는 함수
 * @param {HTMLElement} input - 숫자 입력 필드 엘리먼트
 */
function applyNumberFormat(input) {
    // 현재 커서 위치 저장
    const start = input.selectionStart;
    
    // 입력된 값에서 콤마 제거
    let value = input.value.replace(/,/g, '');
    
    // 숫자만 남기기
    value = value.replace(/[^\d]/g, '');
    
    // 콤마 포맷팅 적용
    if (value) {
        const formattedValue = numberWithCommas(value);
        
        // 값이 변경된 경우에만 업데이트
        if (input.value !== formattedValue) {
            // 커서 위치 계산
            const beforeCommas = input.value.substr(0, start).replace(/[^\d]/g, '').length;
            input.value = formattedValue;
            
            // 새로운 커서 위치 계산
            const afterCommas = formattedValue.substr(0, start).replace(/[^\d]/g, '').length;
            const newPosition = start + (afterCommas - beforeCommas);
            
            // 커서 위치 설정
            input.setSelectionRange(newPosition, newPosition);
        }
    } else {
        input.value = '';
    }
}

/**
 * 숫자 입력 필드에 이벤트 리스너를 추가하는 함수
 * @param {string} selector - 숫자 입력 필드의 CSS 선택자
 */
function initNumberFormatting(selector) {
    const inputs = document.querySelectorAll(selector);
    
    inputs.forEach(input => {
        // input 이벤트에 포맷팅 적용
        input.addEventListener('input', function(e) {
            applyNumberFormat(this);
        });

        // 숫자와 콤마만 입력 가능하도록 제한
        input.addEventListener('keypress', function(e) {
            if (!/[\d]/.test(e.key) && e.key !== ',') {
                e.preventDefault();
            }
        });
    });
}

/**
 * 숫자를 한국어 화폐 단위로 변환하는 함수
 * 10000만원 → 1억원, 10억 5000만원 등으로 변환
 * 
 * @param {number|string} value - 변환할 숫자 또는 문자열
 * @param {boolean} includeUnit - '원' 단위 포함 여부 (기본값: true)
 * @param {boolean} includeManWon - 모든 결과에 '만원' 단위 표시 여부 (기본값: false)
 * @returns {string} 한국어 화폐 단위로 변환된 문자열
 */
function formatKoreanCurrency(value, includeUnit = true, includeManWon = false) {
    // 문자열이나 기타 타입을 숫자로 변환
    const num = parseFloat(value.toString().replace(/[^\d.-]/g, ''));
    
    // 유효하지 않은 값이면 빈 문자열 반환
    if (isNaN(num)) return '';
    
    // 절대값 사용 (음수 처리를 위해)
    const absNum = Math.abs(num);
    
    // 원 단위 문자열
    const unit = includeUnit ? '원' : '';
    
    // 조 단위 (1조 = 1,000,000,000,000)
    if (absNum >= 1000000000000) {
        const jo = Math.floor(absNum / 1000000000000);
        const eok = Math.floor((absNum % 1000000000000) / 100000000);
        const man = Math.floor((absNum % 100000000) / 10000);
        
        let result = jo + '조';
        
        if (eok > 0) {
            result += ' ' + eok + '억';
        }
        
        if (man > 0) {
            result += ' ' + man + '만';
        }
        
        return (num < 0 ? '-' : '') + result + unit;
    }
    
    // 억 단위 (1억 = 100,000,000)
    if (absNum >= 100000000) {
        const eok = Math.floor(absNum / 100000000);
        const man = Math.floor((absNum % 100000000) / 10000);
        
        let result = eok + '억';
        
        if (man > 0) {
            result += ' ' + man + '만';
        }
        
        return (num < 0 ? '-' : '') + result + unit;
    }
    
    // 만 단위 (1만 = 10,000)
    if (absNum >= 10000 || includeManWon) {
        const man = Math.floor(absNum / 10000);
        const remainder = Math.floor(absNum % 10000);
        
        let result = man + '만';
        
        // 1만 미만의 값이 있고 그 값이 1000 이상인 경우에만 추가
        if (remainder >= 1000) {
            result += ' ' + numberWithCommas(remainder);
        }
        
        return (num < 0 ? '-' : '') + result + unit;
    }
    
    // 1만 미만은 그냥 천단위 콤마로 표시
    return (num < 0 ? '-' : '') + numberWithCommas(Math.floor(absNum)) + unit;
}
