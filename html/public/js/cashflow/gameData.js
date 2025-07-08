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

// 데이터베이스 기반 저장 관리
const DatabaseManager = {
    // 현재 세션 키
    currentSessionKey: null,
    
    // 사용자 인증 확인 (서버에서 Auth::user()->mq_user_id로 처리)
    isAuthenticated: () => {
        // 임시로 항상 true 반환 (서버에서 인증 확인)
        console.log('인증 상태 확인 - 임시로 true 반환');
        return true;
        
        // 향후 사용할 코드:
        // const authMeta = document.querySelector('meta[name="auth-user"]');
        // return authMeta && authMeta.getAttribute('content') !== '';
    },
    
    // API 호출 헬퍼
    apiCall: async (url, method = 'GET', data = null) => {
        console.log(`=== API 호출: ${method} ${url} ===`);
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('CSRF 토큰:', csrfToken);
            
            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            };
            
            if (data) {
                options.body = JSON.stringify(data);
                console.log('요청 데이터:', data);
                console.log('요청 본문:', options.body);
            }
            
            console.log('요청 옵션:', options);
            
            const response = await fetch(url, options);
            
            console.log('응답 상태:', response.status, response.statusText);
            console.log('응답 헤더:', response.headers);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('HTTP 오류 응답:', errorText);
                throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
            }
            
            const responseData = await response.json();
            console.log('응답 데이터:', responseData);
            
            return responseData;
        } catch (error) {
            console.error('API 호출 실패:', error);
            console.error('오류 타입:', error.constructor.name);
            console.error('오류 메시지:', error.message);
            throw error;
        }
    },
    
    // 게임 상태 저장
    saveGameState: async (gameState) => {
        console.log('=== 게임 상태 저장 시작 ===');
        console.log('gameState:', gameState);
        
        try {
            // 인증 확인
            if (!DatabaseManager.isAuthenticated()) {
                console.error('사용자 인증이 필요합니다.');
                return false;
            }
            
            const data = {
                gameState: gameState,
                sessionKey: DatabaseManager.currentSessionKey
            };
            
            // sessionKey가 없으면 gameState에서 가져오기 시도
            if (!data.sessionKey && gameState.sessionKey) {
                data.sessionKey = gameState.sessionKey;
                DatabaseManager.currentSessionKey = gameState.sessionKey;
                console.log('gameState에서 sessionKey 복원:', gameState.sessionKey);
            }
            
            // 여전히 sessionKey가 없으면 경고
            if (!data.sessionKey) {
                console.warn('sessionKey가 설정되지 않았습니다. 새로운 게임이 생성될 수 있습니다.');
            }
            
            console.log('API 호출 데이터:', data);
            console.log('API URL: /api/cashflow/save');
            
            const response = await DatabaseManager.apiCall('/api/cashflow/save', 'POST', data);
            
            console.log('API 응답:', response);
            
            if (response.success) {
                DatabaseManager.currentSessionKey = response.sessionKey;
                console.log('게임 상태 저장 성공:', response.sessionKey);
                
                // 로컬 스토리지에도 백업 저장
                try {
                    localStorage.setItem('cashflowGameState', JSON.stringify(gameState));
                    localStorage.setItem('cashflowSessionKey', response.sessionKey);
                } catch (e) {
                    console.warn('로컬 스토리지 백업 저장 실패:', e);
                }
                
                return true;
            } else {
                console.error('게임 상태 저장 실패:', response.error);
                return false;
            }
        } catch (error) {
            console.error('게임 상태 저장 중 오류:', error);
            console.error('오류 스택:', error.stack);
            
            // 오프라인 대응: 로컬 스토리지에 저장
            try {
                localStorage.setItem('cashflowGameState', JSON.stringify(gameState));
                localStorage.setItem('cashflowPendingSync', 'true');
                console.warn('오프라인 모드: 로컬 스토리지에 저장됨');
                return true;
            } catch (e) {
                console.error('로컬 스토리지 저장도 실패:', e);
                return false;
            }
        }
    },
    
    // 실시간 로그 저장 (중요한 이벤트용)
    saveGameLogOnly: async (gameState) => {
        console.log('=== 게임 로그만 즉시 저장 시작 ===');
        
        try {
            // 인증 확인
            if (!DatabaseManager.isAuthenticated()) {
                console.error('사용자 인증이 필요합니다.');
                return false;
            }
            
            const data = {
                gameState: {
                    gameLog: gameState.gameLog // 로그만 전송
                },
                sessionKey: DatabaseManager.currentSessionKey || gameState.sessionKey,
                logOnly: true // 로그만 저장하는 플래그
            };
            
            console.log('로그 즉시 저장 API 호출:', data);
            
            const response = await DatabaseManager.apiCall('/api/cashflow/save', 'POST', data);
            
            if (response.success) {
                console.log('게임 로그 즉시 저장 성공');
                return true;
            } else {
                console.error('게임 로그 즉시 저장 실패:', response.error);
                return false;
            }
        } catch (error) {
            console.error('게임 로그 즉시 저장 중 오류:', error);
            return false;
        }
    },
    
    // 게임 상태 로드
    loadGameState: async () => {
        try {
            // 세션 키가 없으면 로컬 스토리지에서 복원 시도
            if (!DatabaseManager.currentSessionKey) {
                DatabaseManager.currentSessionKey = localStorage.getItem('cashflowSessionKey');
            }
            
            if (!DatabaseManager.currentSessionKey) {
                console.log('세션 키가 없음, 로컬 스토리지에서 로드 시도');
                return DatabaseManager.loadFromLocalStorage();
            }
            
            const data = {
                sessionKey: DatabaseManager.currentSessionKey
            };
            
            const response = await DatabaseManager.apiCall('/api/cashflow/load', 'POST', data);
            
            if (response.success) {
                console.log('게임 상태 로드 성공');
                
                // 로컬 스토리지에도 백업
                try {
                    localStorage.setItem('cashflowGameState', JSON.stringify(response.gameState));
                } catch (e) {
                    console.warn('로컬 스토리지 백업 실패:', e);
                }
                
                return response.gameState;
            } else {
                console.error('게임 상태 로드 실패:', response.error);
                return DatabaseManager.loadFromLocalStorage();
            }
        } catch (error) {
            console.error('게임 상태 로드 중 오류:', error);
            console.log('오프라인 모드: 로컬 스토리지에서 로드');
            return DatabaseManager.loadFromLocalStorage();
        }
    },
    
    // 로컬 스토리지에서 로드 (오프라인 대응)
    loadFromLocalStorage: () => {
        try {
            const saved = localStorage.getItem('cashflowGameState');
            return saved ? JSON.parse(saved) : null;
        } catch (error) {
            console.error('로컬 스토리지 로드 실패:', error);
            return null;
        }
    },
    
    // 저장된 게임 상태 삭제
    clearGameState: async () => {
        try {
            if (DatabaseManager.currentSessionKey) {
                const data = {
                    sessionKey: DatabaseManager.currentSessionKey
                };
                
                await DatabaseManager.apiCall('/api/cashflow/game', 'DELETE', data);
                console.log('서버에서 게임 삭제 완료');
            }
            
            // 로컬 스토리지도 삭제
            localStorage.removeItem('cashflowGameState');
            localStorage.removeItem('cashflowSessionKey');
            localStorage.removeItem('cashflowPendingSync');
            
            DatabaseManager.currentSessionKey = null;
            return true;
        } catch (error) {
            console.error('게임 상태 삭제 실패:', error);
            
            // 로컬 스토리지만 삭제
            localStorage.removeItem('cashflowGameState');
            localStorage.removeItem('cashflowSessionKey');
            DatabaseManager.currentSessionKey = null;
            return false;
        }
    },
    
    // 사용자의 게임 목록 조회
    getUserGames: async () => {
        try {
            // 인증 확인
            if (!DatabaseManager.isAuthenticated()) {
                console.error('사용자 인증이 필요합니다.');
                return [];
            }
            
            const data = {}; // 서버에서 Auth::user()->mq_user_id로 처리
            
            const response = await DatabaseManager.apiCall('/api/cashflow/games', 'POST', data);
            
            if (response.success) {
                return response.games;
            } else {
                console.error('게임 목록 조회 실패:', response.error);
                return [];
            }
        } catch (error) {
            console.error('게임 목록 조회 중 오류:', error);
            return [];
        }
    },
    
    // 세션 키 설정
    setSessionKey: (sessionKey) => {
        DatabaseManager.currentSessionKey = sessionKey;
        if (sessionKey) {
            localStorage.setItem('cashflowSessionKey', sessionKey);
        }
    },
    
    // API 연결 테스트
    testConnection: async () => {
        console.log('=== API 연결 테스트 시작 ===');
        try {
            const response = await DatabaseManager.apiCall('/api/cashflow/test', 'POST', {});
            console.log('테스트 결과:', response);
            return response;
        } catch (error) {
            console.error('API 연결 테스트 실패:', error);
            return { success: false, error: error.message };
        }
    },
    
    // 동기화 상태 확인 및 처리
    checkPendingSync: async () => {
        // 인증되지 않은 경우 동기화 불가
        if (!DatabaseManager.isAuthenticated()) {
            console.log('사용자 인증이 필요하여 동기화를 건너뜁니다.');
            return;
        }
        
        const pending = localStorage.getItem('cashflowPendingSync');
        if (pending === 'true') {
            console.log('대기 중인 동기화 발견, 동기화 시도');
            const gameState = DatabaseManager.loadFromLocalStorage();
            if (gameState) {
                const success = await DatabaseManager.saveGameState(gameState);
                if (success) {
                    localStorage.removeItem('cashflowPendingSync');
                    console.log('대기 중인 동기화 완료');
                }
            }
        }
    },
    
    // 플레이어 통계 (로컬 스토리지 유지)
    savePlayerStats: (stats) => {
        try {
            localStorage.setItem('cashflow_player_stats', JSON.stringify(stats));
            return true;
        } catch (error) {
            console.error('플레이어 통계 저장 실패:', error);
            return false;
        }
    },
    
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

// 하위 호환성을 위한 별칭
const StorageManager = DatabaseManager;

// 전역 테스트 함수들 (브라우저 콘솔에서 사용)
window.testCashflowAPI = async () => {
    console.log('캐시플로우 API 테스트 시작...');
    const result = await DatabaseManager.testConnection();
    console.log('테스트 결과:', result);
    return result;
};

window.debugCashflowSave = async () => {
    console.log('캐시플로우 저장 테스트...');
    const mockGameState = {
        player: {
            name: 'TEST',
            profession: 'TEST',
            cash: 1000
        },
        gameStarted: true,
        gameLog: ['테스트 로그']
    };
    
    const result = await DatabaseManager.saveGameState(mockGameState);
    console.log('저장 테스트 결과:', result);
    return result;
};

window.debugCashflowLoad = async () => {
    console.log('캐시플로우 로드 테스트...');
    const result = await DatabaseManager.loadGameState();
    console.log('로드 테스트 결과:', result);
    
    if (result && result.player && result.player.liabilities) {
        console.log('부채 데이터 상세:', result.player.liabilities);
        result.player.liabilities.forEach((liability, index) => {
            console.log(`부채 ${index + 1}:`, {
                name: liability.name,
                type: liability.type,
                amount: liability.amount,
                monthlyPayment: liability.monthlyPayment,
                amountType: typeof liability.amount,
                monthlyPaymentType: typeof liability.monthlyPayment
            });
        });
    } else {
        console.log('부채 데이터가 없습니다.');
    }
    
    return result;
};

window.debugLiabilities = () => {
    if (typeof gameState !== 'undefined' && gameState.player && gameState.player.liabilities) {
        console.log('현재 게임 상태의 부채 데이터:', gameState.player.liabilities);
        gameState.player.liabilities.forEach((liability, index) => {
            console.log(`부채 ${index + 1}:`, {
                name: liability.name,
                type: liability.type,
                amount: liability.amount,
                monthlyPayment: liability.monthlyPayment,
                amountType: typeof liability.amount,
                monthlyPaymentType: typeof liability.monthlyPayment,
                isZero: liability.amount === 0
            });
        });
    } else {
        console.log('현재 게임 상태에 부채 데이터가 없습니다.');
    }
};

window.debugStocks = () => {
    console.log('=== 주식 데이터 디버깅 ===');
    
    // gameState 찾기 (전역 또는 게임 인스턴스에서)
    let gameState = null;
    if (typeof window.cashflowGame !== 'undefined' && window.cashflowGame.gameState) {
        gameState = window.cashflowGame.gameState;
    } else if (typeof window.gameState !== 'undefined') {
        gameState = window.gameState;
    }
    
    if (gameState && gameState.player) {
        console.log('gameState.player.stocks:', gameState.player.stocks);
        console.log('gameState.player.funds:', gameState.player.funds);
        
        if (gameState.player.stocks && Object.keys(gameState.player.stocks).length > 0) {
            console.log('보유 주식 상세:');
            Object.entries(gameState.player.stocks).forEach(([symbol, data]) => {
                console.log(`${symbol}:`, {
                    shares: data.shares,
                    averagePrice: data.averagePrice,
                    totalInvested: data.totalInvested,
                    monthlyDividend: data.monthlyDividend,
                    dataTypes: {
                        shares: typeof data.shares,
                        averagePrice: typeof data.averagePrice,
                        totalInvested: typeof data.totalInvested,
                        monthlyDividend: typeof data.monthlyDividend
                    }
                });
            });
        } else {
            console.log('보유 주식이 없습니다.');
        }
        
        if (gameState.player.funds && Object.keys(gameState.player.funds).length > 0) {
            console.log('보유 펀드 상세:');
            Object.entries(gameState.player.funds).forEach(([symbol, data]) => {
                console.log(`${symbol}:`, data);
            });
        } else {
            console.log('보유 펀드가 없습니다.');
        }
    } else {
        console.log('현재 게임 상태가 없습니다.');
    }
};

window.debugSaveData = async () => {
    console.log('=== 저장 데이터 디버깅 ===');
    
    // gameState 찾기 (전역 또는 게임 인스턴스에서)
    let gameState = null;
    if (typeof window.cashflowGame !== 'undefined' && window.cashflowGame.gameState) {
        gameState = window.cashflowGame.gameState;
    } else if (typeof window.gameState !== 'undefined') {
        gameState = window.gameState;
    }
    
    if (gameState) {
        console.log('저장될 gameState 전체:', gameState);
        console.log('저장될 player.stocks:', gameState.player?.stocks);
        console.log('저장될 player.funds:', gameState.player?.funds);
        
        // 실제 저장 테스트
        try {
            console.log('수동 저장 테스트 시작...');
            const result = await DatabaseManager.saveGameState(gameState);
            console.log('수동 저장 결과:', result);
        } catch (error) {
            console.error('수동 저장 실패:', error);
        }
    } else {
        console.log('gameState가 정의되지 않았습니다.');
    }
};

// 데이터베이스 저장 카운터 디버깅
window.debugSaveCounter = () => {
    console.log('=== 데이터베이스 저장 카운터 ===');
    
    // 저장 카운터 초기화
    window._saveCounter = 0;
    
    // 기존 saveGameState 함수 백업
    const originalSaveGameState = DatabaseManager.saveGameState;
    
    // 래퍼 함수로 교체
    DatabaseManager.saveGameState = async function(gameState) {
        window._saveCounter++;
        console.log(`📝 저장 호출 #${window._saveCounter}:`, {
            호출시각: new Date().toLocaleTimeString(),
            sessionKey: DatabaseManager.currentSessionKey,
            stocks: gameState.player?.stocks ? Object.keys(gameState.player.stocks) : '없음',
            stocksData: gameState.player?.stocks
        });
        
        // 스택 트레이스 출력
        console.trace('저장 호출 스택');
        
        return await originalSaveGameState.call(this, gameState);
    };
    
    console.log('저장 카운터 활성화됨. 이제 주식을 구매해보세요.');
};

// 주식 구매 과정 상세 디버깅
window.debugStockPurchase = () => {
    console.log('=== 주식 구매 과정 디버깅 ===');
    
    if (typeof window.cashflowGame !== 'undefined' && window.cashflowGame.gameState) {
        const gameState = window.cashflowGame.gameState;
        
        console.log('구매 전 주식 현황:');
        if (gameState.player?.stocks) {
            Object.entries(gameState.player.stocks).forEach(([symbol, data]) => {
                console.log(`${symbol}: ${data.shares}주, 평균가: ${data.averagePrice}, 총투자: ${data.totalInvested}`);
            });
        } else {
            console.log('보유 주식 없음');
        }
        
        console.log('구매 전 게임 로그 개수:', gameState.gameLog?.length || 0);
        console.log('최근 5개 로그:');
        if (gameState.gameLog) {
            gameState.gameLog.slice(0, 5).forEach((log, index) => {
                console.log(`${index + 1}. ${log.message}`);
            });
        }
    } else {
        console.log('게임 상태를 찾을 수 없습니다.');
    }
};

window.debugGameLog = () => {
    console.log('=== 게임 로그 디버깅 ===');
    
    // gameState 찾기 (전역 또는 게임 인스턴스에서)
    let gameState = null;
    if (typeof window.cashflowGame !== 'undefined' && window.cashflowGame.gameState) {
        gameState = window.cashflowGame.gameState;
    } else if (typeof window.gameState !== 'undefined') {
        gameState = window.gameState;
    }
    
    if (gameState && gameState.gameLog) {
        console.log('현재 게임 로그 개수:', gameState.gameLog.length);
        console.log('현재 게임 로그 전체:', gameState.gameLog);
        
        if (gameState.gameLog.length > 0) {
            console.log('최근 5개 로그:');
            gameState.gameLog.slice(0, 5).forEach((log, index) => {
                console.log(`${index + 1}:`, {
                    message: log.message,
                    type: log.type,
                    timestamp: log.timestamp,
                    details: log.details
                });
            });
        }
    } else {
        console.log('게임 로그가 없습니다.');
        if (gameState) {
            console.log('gameState는 존재하지만 gameLog가 없음:', gameState);
        }
    }
};

window.testAddGameLog = () => {
    console.log('=== 게임 로그 추가 테스트 ===');
    
    if (typeof window.cashflowGame !== 'undefined' && window.cashflowGame.addGameLog) {
        const testMessage = `테스트 로그 - ${new Date().toLocaleTimeString()}`;
        console.log('테스트 로그 추가:', testMessage);
        
        window.cashflowGame.addGameLog(testMessage, 'info');
        
        // 추가 후 확인
        setTimeout(() => {
            console.log('로그 추가 후 게임 로그 개수:', window.cashflowGame.gameState.gameLog.length);
            console.log('방금 추가된 로그:', window.cashflowGame.gameState.gameLog[0]);
        }, 100);
    } else {
        console.log('cashflowGame 인스턴스 또는 addGameLog 메소드를 찾을 수 없습니다.');
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


