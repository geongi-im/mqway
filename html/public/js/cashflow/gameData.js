// 게임 전역 설정 및 유틸리티 함수

// 게임 설정
const GAME_CONFIG = {
    EMERGENCY_LOAN_RATE: 0.1, // 응급 대출 월 이자율 (10%)
    INVESTMENT_LOAN_RATE: 0.1 // 투자 대출 월 이자율 (10%)
};

// 유틸리티 함수들
const GameUtils = {
    // 숫자를 통화 형식으로 포맷
    formatCurrency: (amount) => {
        // undefined, null, NaN 체크
        if (amount === undefined || amount === null || isNaN(amount)) {
            amount = 0;
        }
        return `$${amount.toLocaleString()}`;
    },

    // 랜덤 정수 생성 (min 이상 max 이하)
    getRandomInt: (min, max) => {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    },

    // 배열에서 랜덤 요소 선택
    getRandomElement: (array) => {
        return array[Math.floor(Math.random() * array.length)];
    },

    // 현재 시간 문자열 반환
    getCurrentTimeString: () => {
        return new Date().toLocaleTimeString('ko-KR');
    },

    // 날짜 문자열 반환
    getCurrentDateString: () => {
        return new Date().toLocaleDateString('ko-KR');
    },

    // UUID 생성 함수
    generateUUID: () => {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    },

    // ROI 계산 (투자 수익률)
    calculateROI: (monthlyCashflow, totalInvestment) => {
        if (totalInvestment === 0) return 0;
        return (monthlyCashflow * 12) / totalInvestment * 100;
    },

    // 대출 월 상환액 계산 (원리금균등상환 방식)
    calculateMonthlyPayment: (principal, annualRate, years) => {
        const monthlyRate = annualRate / 12;
        const totalPayments = years * 12;
        
        if (monthlyRate === 0) {
            return principal / totalPayments;
        }
        
        return principal * (monthlyRate * Math.pow(1 + monthlyRate, totalPayments)) / 
               (Math.pow(1 + monthlyRate, totalPayments) - 1);
    },

    // USD to KRW 변환 (실시간 환율 사용)
    convertUsdToKrw: async (usdAmount) => {
        if (usdAmount === undefined || usdAmount === null || isNaN(usdAmount) || usdAmount === 0) {
            return 0;
        }
        
        try {
            const exchangeRate = await ExchangeRateService.getUsdToKrwRate();
            return Math.round(usdAmount * exchangeRate);
        } catch (error) {
            console.warn('환율 API 호출 실패, 기본 환율 사용:', error);
            const fallbackRate = 1300; // 기본 환율
            return Math.round(usdAmount * fallbackRate);
        }
    },

    // KRW 금액을 포맷 (천 단위 콤마)
    formatKrw: (amount) => {
        if (amount === undefined || amount === null || isNaN(amount)) {
            amount = 0;
        }
        return amount.toLocaleString('ko-KR');
    }
};

// 환율 서비스
const ExchangeRateService = {
    cachedRate: null,
    lastFetchTime: null,
    cacheTimeMinutes: 30, // 30분 캐시

    // USD to KRW 환율 가져오기
    async getUsdToKrwRate() {
        // 캐시된 환율이 있고 30분 이내라면 캐시 사용
        const now = new Date().getTime();
        if (this.cachedRate && this.lastFetchTime && 
            (now - this.lastFetchTime) < this.cacheTimeMinutes * 60 * 1000) {
            return this.cachedRate;
        }

        try {
            // 무료 환율 API 사용 (exchangerate-api.com)
            const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
            const data = await response.json();
            
            if (data.rates && data.rates.KRW) {
                this.cachedRate = data.rates.KRW;
                this.lastFetchTime = now;
                console.log(`환율 업데이트: 1 USD = ${this.cachedRate} KRW`);
                return this.cachedRate;
            } else {
                throw new Error('환율 데이터가 없습니다');
            }
        } catch (error) {
            console.warn('환율 API 호출 실패:', error);
            // 기본 환율 반환
            const fallbackRate = 1300;
            if (!this.cachedRate) {
                this.cachedRate = fallbackRate;
            }
            return this.cachedRate;
        }
    }
};

// 로컬 스토리지 관리
const StorageManager = {
    // 게임 상태 저장
    saveGameState: (gameState) => {
        try {
            localStorage.setItem('cashflowGameState', JSON.stringify(gameState));
            return true;
        } catch (error) {
            console.error('게임 상태 저장 실패:', error);
            return false;
        }
    },

    // 게임 상태 로드
    loadGameState: () => {
        try {
            const saved = localStorage.getItem('cashflowGameState');
            return saved ? JSON.parse(saved) : null;
        } catch (error) {
            console.error('게임 상태 로드 실패:', error);
            return null;
        }
    },

    // 저장된 게임 상태 삭제
    clearGameState: () => {
        try {
            localStorage.removeItem('cashflowGameState');
            return true;
        } catch (error) {
            console.error('게임 상태 삭제 실패:', error);
            return false;
        }
    },

    // 플레이어 통계 저장
    savePlayerStats: (stats) => {
        try {
            localStorage.setItem('cashflow_player_stats', JSON.stringify(stats));
            return true;
        } catch (error) {
            console.error('플레이어 통계 저장 실패:', error);
            return false;
        }
    },

    // 플레이어 통계 로드
    loadPlayerStats: () => {
        try {
            const saved = localStorage.getItem('cashflow_player_stats');
            return saved ? JSON.parse(saved) : {
                gamesPlayed: 0,
                ratRaceEscapes: 0,
                totalCashEarned: 0,
                bestCashflow: 0
            };
        } catch (error) {
            console.error('플레이어 통계 로드 실패:', error);
            return {
                gamesPlayed: 0,
                ratRaceEscapes: 0,
                totalCashEarned: 0,
                bestCashflow: 0
            };
        }
    }
};

// 재무 계산 유틸리티
const FinancialCalculator = {
    // 총 자산 계산
    calculateTotalAssets: (assets) => {
        return assets.reduce((total, asset) => total + (asset.currentValue || asset.purchasePrice || 0), 0);
    },

    // 총 부채 계산
    calculateTotalLiabilities: (liabilities) => {
        return liabilities.reduce((total, liability) => total + (liability.remainingAmount || 0), 0);
    },

    // 순자산 계산
    calculateNetWorth: (totalAssets, totalLiabilities) => {
        return totalAssets - totalLiabilities;
    },

    // 자산으로부터의 월 소득 계산
    calculatePassiveIncome: (assets) => {
        return assets.reduce((total, asset) => total + (asset.monthlyCashFlow || 0), 0);
    },

    // 부채로부터의 월 지출 계산
    calculateDebtExpenses: (liabilities) => {
        return liabilities.reduce((total, liability) => total + (liability.monthlyPayment || 0), 0);
    },

    // 월 현금흐름 계산
    calculateMonthlyCashFlow: (totalIncome, totalExpenses) => {
        return totalIncome - totalExpenses;
    },

    // 투자로 인한 재무 상태 변화 계산
    calculateInvestmentImpact: (investment) => {
        return {
            cashChange: -(investment.totalCost || 0),
            incomeChange: investment.monthlyCashFlow || 0,
            expenseChange: investment.monthlyExpense || 0,
            assetChange: investment.assetValue || investment.totalCost || 0,
            liabilityChange: investment.debtIncurred || 0
        };
    }
};


