// 캐시플로우 게임 핵심 로직
class CashflowGame {
    constructor(skipAutoLoad = false) {
        this.gameState = {
            player: null,
            gameStarted: false,
            gameLog: [],
            currentSellingAssetId: null
        };
        
        this.cashflowChart = null; // 차트 인스턴스 변수 추가
        this.skipAutoLoad = skipAutoLoad; // 자동 로드 스킵 플래그
        
        // 자녀 관련 카드 목록 (중복 제거를 위한 상수)
        this.CHILD_RELATED_CARDS = [
            "아이들을 위한 장난감 구입", // BUY TOYS FOR YOUR KIDS
            "생일 파티", // Birthday!
            "아들 대학 등록금", // SON'S COLLEGE TUITION
            "딸의 결혼식", // YOUR DAUGHTER'S WEDDING
            "자녀 치아교정" // YOUR CHILD NEEDS BRACES
        ];
        
        // 모든 카드에 ID 할당
        this.assignIdsToCards();
        
        this.init();
        
        // 윈도우 리사이즈 이벤트 핸들러 (한 번만 등록)
        window.addEventListener('resize', () => {
            if (this.cashflowChart) {
                this.cashflowChart.resize();
            }
        });
    }
    
    // 모든 카드에 고유 ID 할당
    assignIdsToCards() {
        const assignIds = (cardArray) => {
            if (!cardArray) return;
            
            cardArray.forEach((card, index) => {
                // 심볼 또는 타이틀에서 식별자 추출
                let identifier = '';
                
                if (card.assetDetails && card.assetDetails.assetType === 'Stock') {
                    // 주식인 경우 심볼과 가격으로 식별
                    const symbol = this.getStockSymbolFromTitle(card.title) || 'STOCK';
                    identifier = `${symbol}_${card.cost}`;
                } else if (card.title) {
                    // 타이틀에서 첫 단어 사용
                    identifier = card.title.split(' ')[0].toLowerCase();
                }
                
                // 고유 ID 생성
                card.id = `card_${identifier}_${index}`;
            });
        };
        
        // 각 카드 타입에 ID 할당
        assignIds(CARD_DATA.SmallDeals);
        assignIds(CARD_DATA.BigDeals);
        assignIds(CARD_DATA.Doodads);
    }

    async init() {
        console.log('=== 게임 초기화 시작, skipAutoLoad:', this.skipAutoLoad, '===');
        
        this.renderProfessionsList();
        this.bindEvents();
        this.bindNavigationEvents();
        this.setupScrollHandler(); // 스크롤 핸들러 설정 추가
        
        // skipAutoLoad가 true이면 UI 관련 작업은 수행하지 않음
        if (this.skipAutoLoad) {
            console.log('자동 로드 스킵됨 - Continue 모드 또는 수동 초기화');
            return;
        }
        
        // 대기 중인 동기화 확인
        try {
            await DatabaseManager.checkPendingSync();
        } catch (error) {
            console.warn('동기화 확인 실패:', error);
        }
        
        // 저장된 게임 상태 확인
        try {
            const savedGameState = await StorageManager.loadGameState();
            if (savedGameState && savedGameState.gameStarted) {
                // 저장된 게임이 있으면 사용자에게 선택권 제공
                this.showGameRestoreOption(savedGameState);
                return;
            }
        } catch (error) {
            console.warn('저장된 게임 로드 실패:', error);
        }
        
        // 저장된 게임이 없으면 새 게임 시작 화면 표시
        this.showNewGameScreen();
    }
    
    // 게임 복원 옵션 표시 (confirm 제거 - 항상 새 게임 시작)
    showGameRestoreOption(savedGameState) {
        // confirm 로직 제거 - 항상 새 게임 시작
        // 최근 기록은 helper.blade.php에서 처리됨
        this.showNewGameScreen();
    }
    
    // 새 게임 시작 화면 표시
    showNewGameScreen() {
        console.log('=== showNewGameScreen 호출됨 ===');
        console.trace('showNewGameScreen 호출 스택:');
        
        // 게임 시작 버튼 컨테이너 표시
        const startBtn = document.getElementById('start-game-fixed-button');
        if (startBtn) {
            startBtn.style.display = 'block';
            startBtn.classList.remove('hidden');
            console.log('게임 시작 버튼을 표시했습니다');
        }
        
        // 모든 탭 콘텐츠 숨기기 (직업 선택 화면만 표시)
        const allTabContents = document.querySelectorAll('.tab-content');
        allTabContents.forEach(tab => {
            tab.style.display = 'none';
            tab.classList.add('hidden');
        });
        console.log('모든 탭 콘텐츠를 숨겼습니다');
        
        // 하단 네비게이션 숨기기
        const bottomNav = document.getElementById('bottom-nav');
        if (bottomNav) {
            bottomNav.style.display = 'none';
            bottomNav.classList.add('hidden');
            console.log('하단 네비게이션을 숨겼습니다');
        }
        
        console.log('=== showNewGameScreen 완료 ===');
    }

    // 게임 시작
    startGame(playerName, professionIndex) {
        // 게임 시작 시 기존 저장된 데이터 완전 삭제 (새 게임으로 시작)
        StorageManager.clearGameState();
        
        // 게임 상태 완전 초기화
        this.gameState = {
            player: null,
            gameStarted: false,
            gameLog: [],
            currentSellingAssetId: null
        };
        
        // 차트 정리 (혹시 남아있을 수 있는 차트들)
        if (this.cashflowChart) {
            this.cashflowChart.dispose();
            this.cashflowChart = null;
        }
        if (this.ratRaceChart) {
            this.ratRaceChart.dispose();
            this.ratRaceChart = null;
        }
        
        const selectedProfession = PROFESSION_DATA[professionIndex];
        const playerDream = document.getElementById('player-dream').value.trim() || "재정적 자유";
        const dreamCost = parseFloat(document.getElementById('dream-cost').value);
        
        if (!selectedProfession) {
            this.showModalNotification("오류", "유효하지 않은 직업입니다.");
            return;
        }

        // 플레이어 객체 생성
        this.gameState.player = {
            name: playerName,
            profession: selectedProfession.jobTitle,
            dream: playerDream,
            dreamCost: dreamCost,
            cash: selectedProfession.balanceSheet.assets.cash,
            monthlyCashFlow: selectedProfession.financialSummary.monthlyCashFlow,
            totalIncome: selectedProfession.incomeStatement.totalIncome,
            totalExpenses: selectedProfession.expenses.totalExpenses,
            salary: selectedProfession.incomeStatement.salary,
            passiveIncome: selectedProfession.incomeStatement.passiveIncome || 0,
            assets: [],
            liabilities: this.initializeLiabilities(selectedProfession.balanceSheet.liabilities, selectedProfession.expenses),
            expenses: {
                taxes: selectedProfession.expenses.taxes,
                homePayment: selectedProfession.expenses.homeMortgagePayment,
                schoolLoan: selectedProfession.expenses.schoolLoanPayment,
                carLoan: selectedProfession.expenses.carLoanPayment,
                creditCard: selectedProfession.expenses.creditCardPayment,
                retail: selectedProfession.expenses.retailExpenses,
                other: selectedProfession.expenses.otherExpenses,
                children: 0,
                totalExpenses: selectedProfession.expenses.totalExpenses,
                childrenCount: 0,
                perChildExpense: 200 // 자녀 1명당 월 지출
            },
            hasChild: false,
            stocks: {},
            funds: {},
            emergencyLoans: []
        };

        this.gameState.gameStarted = true;
        this.gameState.gameLog = [];
        
        // 초기 재무 상태 계산
        this.recalculatePlayerFinancials();
        
        // 디버깅: 초기 상태 확인
        console.log('게임 시작 후 플레이어 상태:', {
            totalIncome: this.gameState.player.totalIncome,
            totalExpenses: this.gameState.player.totalExpenses,
            monthlyCashFlow: this.gameState.player.monthlyCashFlow,
            expenses: this.gameState.player.expenses
        });
        
        // 게임 로그 추가
        this.addGameLog(`${playerName}님이 ${selectedProfession.jobTitle}로 게임을 시작했습니다!`);
        this.addGameLog(`꿈: ${playerDream} (목표 금액: ${GameUtils.formatCurrency(dreamCost)})`);

        // UI 전환
        this.switchToGameUI();
        this.updateUI();
        
        // 상태 저장
        StorageManager.saveGameState(this.gameState);
        
        // 게임 시작 버튼 컨테이너 숨기기
        const startBtn = document.getElementById('start-game-fixed-button');
        if (startBtn) {
            startBtn.style.display = 'none';
            startBtn.classList.add('hidden');
        }
    }

    // 게임 초기화
    resetGame() {
        // 상태 초기화
        this.gameState = {
            player: null,
            gameStarted: false,
            gameLog: [],
            currentSellingAssetId: null
        };
        
        // 로컬 스토리지에서 삭제
        StorageManager.clearGameState();
        
        // 차트 정리
        if (this.cashflowChart) {
            this.cashflowChart.dispose();
            this.cashflowChart = null;
        }
        if (this.ratRaceChart) {
            this.ratRaceChart.dispose();
            this.ratRaceChart = null;
        }
        
        // UI 초기화 - 직업 선택 화면으로 돌아가기
        const professionSelection = document.getElementById('profession-selection');
        if (professionSelection) {
            professionSelection.style.display = 'block';
            professionSelection.classList.remove('hidden');
        }
        
        const bottomNav = document.getElementById('bottom-nav');
        if (bottomNav) {
            bottomNav.style.display = 'none';
            bottomNav.classList.add('hidden');
        }
        
        const dashboardTab = document.getElementById('tab-content-dashboard');
        if (dashboardTab) {
            dashboardTab.style.display = 'none';
            dashboardTab.classList.add('hidden');
        }
        
        // 모든 탭 콘텐츠 숨기기
        const allTabContents = document.querySelectorAll('.tab-content');
        allTabContents.forEach(tab => {
            tab.style.display = 'none';
            tab.classList.add('hidden');
        });
        
        // 입력 필드 초기화
        document.getElementById('player-name').value = '';
        document.getElementById('player-dream').value = '';
        document.getElementById('dream-cost').value = '100000';
        
        // 선택된 직업 해제
        const allCards = document.querySelectorAll('.profession-card');
        allCards.forEach(card => card.classList.remove('selected'));
        
        // 시작 버튼 상태 초기화
        const startBtn = document.getElementById('start-game-btn');
        startBtn.classList.add('opacity-50', 'cursor-not-allowed');
        startBtn.disabled = true;
        delete startBtn.dataset.selectedProfession;
        
        // 게임 시작 버튼 컨테이너 표시
        const startBtnReset = document.getElementById('start-game-fixed-button');
        if (startBtnReset) {
            startBtnReset.style.display = 'block';
            startBtnReset.classList.remove('hidden');
        }
        
        this.addGameLog("게임이 초기화되었습니다.");
    }

    // 자녀 출산 처리
    handleChildBirth() {
        const player = this.gameState.player;
        if (!player) return;
        
        // 자녀 수 증가
        player.expenses.childrenCount++;
        player.hasChild = true;
        
        // 자녀 관련 월 지출 증가
        player.expenses.children = player.expenses.childrenCount * player.expenses.perChildExpense;
        
        // 총 지출 재계산
        this.recalculatePlayerFinancials();
        
        this.addGameLog(`축하합니다! 자녀가 태어났습니다. (자녀 수: ${player.expenses.childrenCount}명)`);
        this.addGameLog(`월 지출이 ${GameUtils.formatCurrency(player.expenses.perChildExpense)} 증가했습니다.`);
        
        this.updateUI();
        StorageManager.saveGameState(this.gameState);
        
        // 완료 메시지 표시
        setTimeout(() => {
            this.showModalNotification(
                "축하합니다! 🎉",
                `자녀가 태어났습니다!\n\n현재 자녀 수: ${player.expenses.childrenCount}명\n월 양육비: ${GameUtils.formatCurrency(player.expenses.children)}\n\n총 월 지출이 ${GameUtils.formatCurrency(player.expenses.perChildExpense)} 증가했습니다.`
            );
        }, 200);
    }

    // 플레이어 재정 상태 재계산
    recalculatePlayerFinances() {
        const player = this.gameState.player;
        if (!player) return;

        // 총 지출 재계산
        player.totalExpenses = player.expenses.taxes + 
                             player.expenses.homePayment + 
                             player.expenses.schoolLoan + 
                             player.expenses.carLoan + 
                             player.expenses.creditCard + 
                             player.expenses.retail + 
                             player.expenses.other + 
                             player.expenses.children;

        // 월 현금흐름 재계산
        player.monthlyCashFlow = player.totalIncome - player.totalExpenses;
    }

    // 카드 선택 처리
    handleCardSelection(cardType) {
        const cards = CARD_DATA[cardType];
        if (!cards || cards.length === 0) {
            this.showModalNotification("알림", "해당 카드가 없습니다.");
            return;
        }

        // 자녀 관련 카드 필터링 로직
        let availableCards = cards;
        if (cardType === 'Doodads') {
            availableCards = this.filterChildRelatedCards(cards);
        }

        if (availableCards.length === 0) {
            this.showModalNotification("알림", "현재 뽑을 수 있는 카드가 없습니다.");
            return;
        }

        const randomCard = GameUtils.getRandomElement(availableCards);
        this.showCardModal(randomCard);
    }

    // 자녀 관련 카드 필터링
    filterChildRelatedCards(cards) {
        const player = this.gameState.player;
        if (!player) return cards;

        return cards.filter(card => {
            const isChildRelated = this.CHILD_RELATED_CARDS.includes(card.title);
            
            if (isChildRelated) {
                return player.hasChild && player.expenses.childrenCount > 0;
            }
            
            return true;
        });
    }

    // 주식 심볼 추출
    getStockSymbolFromTitle(title) {
        // 다양한 패턴으로 심볼 추출 시도
        
        // 1. "주식 - 2BIG 전력" 형태
        let symbolMatch = title.match(/주식\s*-\s*([A-Z0-9]+)/);
        if (symbolMatch) return symbolMatch[1];
        
        // 2. "2BIG POWER" 형태 (공백 뒤)
        symbolMatch = title.match(/([A-Z0-9]+)\s+[A-Z]/);
        if (symbolMatch) return symbolMatch[1];
        
        // 3. 일반적인 패턴 (첫 번째 대문자+숫자 조합)
        symbolMatch = title.match(/([A-Z0-9]+)/);
        if (symbolMatch) return symbolMatch[1];
        
        return null;
    }

    // 초기 부채 설정
    initializeLiabilities(liabilityData, expenseData) {
        const liabilities = [];
        let id = 1;

        // 주택 대출
        if (liabilityData.homeMortgage && liabilityData.homeMortgage > 0) {
            liabilities.push({
                id: `liability_${id++}`,
                name: "주택 대출",
                type: "Mortgage",
                amount: liabilityData.homeMortgage, // 서버 호환성을 위한 필드
                totalAmount: liabilityData.homeMortgage,
                remainingAmount: liabilityData.homeMortgage,
                monthlyPayment: expenseData.homeMortgagePayment || 0,
                interestRate: 0.05, // 기본 5%
                isInitial: true
            });
        }

        // 학자금 대출
        if (liabilityData.schoolLoans && liabilityData.schoolLoans > 0) {
            liabilities.push({
                id: `liability_${id++}`,
                name: "학자금 대출",
                type: "SchoolLoan",
                amount: liabilityData.schoolLoans, // 서버 호환성을 위한 필드
                totalAmount: liabilityData.schoolLoans,
                remainingAmount: liabilityData.schoolLoans,
                monthlyPayment: expenseData.schoolLoanPayment || 0,
                interestRate: 0.04, // 기본 4%
                isInitial: true
            });
        }

        // 자동차 대출
        if (liabilityData.carLoans && liabilityData.carLoans > 0) {
            liabilities.push({
                id: `liability_${id++}`,
                name: "자동차 대출",
                type: "CarLoan",
                amount: liabilityData.carLoans, // 서버 호환성을 위한 필드
                totalAmount: liabilityData.carLoans,
                remainingAmount: liabilityData.carLoans,
                monthlyPayment: expenseData.carLoanPayment || 0,
                interestRate: 0.06, // 기본 6%
                isInitial: true
            });
        }

        // 신용카드 부채
        if (liabilityData.creditCardDebt && liabilityData.creditCardDebt > 0) {
            liabilities.push({
                id: `liability_${id++}`,
                name: "신용카드 부채",
                type: "CreditCard",
                amount: liabilityData.creditCardDebt, // 서버 호환성을 위한 필드
                totalAmount: liabilityData.creditCardDebt,
                remainingAmount: liabilityData.creditCardDebt,
                monthlyPayment: expenseData.creditCardPayment || 0,
                interestRate: 0.18, // 기본 18%
                isInitial: true
            });
        }

        // 소매 부채
        if (liabilityData.retailDebt && liabilityData.retailDebt > 0) {
            liabilities.push({
                id: `liability_${id++}`,
                name: "소매 부채",
                type: "Retail",
                amount: liabilityData.retailDebt, // 서버 호환성을 위한 필드
                totalAmount: liabilityData.retailDebt,
                remainingAmount: liabilityData.retailDebt,
                monthlyPayment: expenseData.retailExpenses || 0,
                interestRate: 0.12, // 기본 12%
                isInitial: true
            });
        }

        return liabilities;
    }

    // 로그 추가
    addGameLog(message, type = 'info', date = null, details = null) {
        const logEntry = {
            timestamp: date || new Date().toISOString(),
            message: message,
            type: type,
            details: details
        };
        
        this.gameState.gameLog.unshift(logEntry);
        
        // 로그 개수 제한 (최대 200개)
        if (this.gameState.gameLog.length > 200) {
            this.gameState.gameLog = this.gameState.gameLog.slice(0, 200);
        }
        
        // 대시보드 로그 UI 업데이트
        this.updateGameLogUI();
        
        // 실시간 로그 저장 비활성화 (중복 저장 방지)
        // 전체 게임 상태 저장 시에만 로그가 저장되도록 변경
        // this.saveLogToDatabase(logEntry);
    }
    
    // 개별 로그를 즉시 데이터베이스에 저장 (강화된 디바운싱 적용)
    async saveLogToDatabase(logEntry) {
        try {
            // DatabaseManager가 정의되어 있는지 확인
            if (typeof DatabaseManager === 'undefined') {
                console.warn('DatabaseManager가 정의되지 않아 로그 실시간 저장을 건너뜁니다.');
                return;
            }
            
            // 강화된 디바운싱 - 1초 이내 연속 호출 방지
            const currentTime = Date.now();
            if (this._lastLogSaveTime && (currentTime - this._lastLogSaveTime) < 1000) {
                console.log('로그 저장 스킵: 1초 이내 연속 호출');
                
                // 마지막 로그 저장을 지연시킴
                if (this._logSaveTimeout) {
                    clearTimeout(this._logSaveTimeout);
                }
                
                this._logSaveTimeout = setTimeout(() => {
                    this._executeDelayedLogSave(logEntry);
                }, 1000);
                return;
            }
            
            this._lastLogSaveTime = currentTime;
            
            console.log('실시간 로그 저장 시도:', logEntry.message);
            
            const success = await DatabaseManager.saveGameLogOnly(this.gameState);
            
            if (success) {
                console.log('실시간 로그 저장 성공:', logEntry.message);
            } else {
                console.warn('실시간 로그 저장 실패:', logEntry.message);
            }
            
        } catch (error) {
            console.error('실시간 로그 저장 오류:', error);
            // 실시간 저장 실패해도 게임 진행에는 영향 없도록 에러를 삼킴
        }
    }
    
    // 지연된 로그 저장 실행
    async _executeDelayedLogSave(logEntry) {
        try {
            this._lastLogSaveTime = Date.now();
            
            console.log('지연된 실시간 로그 저장 시도:', logEntry.message);
            
            const success = await DatabaseManager.saveGameLogOnly(this.gameState);
            
            if (success) {
                console.log('지연된 실시간 로그 저장 성공:', logEntry.message);
            } else {
                console.warn('지연된 실시간 로그 저장 실패:', logEntry.message);
            }
        } catch (error) {
            console.error('지연된 실시간 로그 저장 오류:', error);
        }
    }

    // 긴급 대출 상환
    repayEmergencyLoan(loanId) {
        try {
            const player = this.gameState.player;
            if (!player) return;
            
            const loanIndex = player.emergencyLoans.findIndex(loan => loan.id === loanId);
            if (loanIndex === -1) {
                this.addGameLog("대출을 찾을 수 없습니다.", 'error');
                return;
            }
            
            const loan = player.emergencyLoans[loanIndex];
            
            if (player.cash < loan.remainingAmount) {
                this.showModalNotification("알림", "현금이 부족합니다.");
                return;
            }
            
            // 대출 상환
            player.cash -= loan.remainingAmount;
            player.expenses.other -= loan.monthlyPayment;
            
            this.addGameLog(`긴급 대출 ${GameUtils.formatCurrency(loan.remainingAmount)}을 상환했습니다.`);
            
            // 대출 목록에서 제거
            player.emergencyLoans.splice(loanIndex, 1);
            
            this.recalculatePlayerFinancials();
            this.updateUI();
            StorageManager.saveGameState(this.gameState);
            
        } catch (error) {
            console.error("긴급 대출 상환 처리 중 오류 발생:", error);
            this.addGameLog("긴급 대출 상환 처리 중 오류가 발생했습니다.", 'error');
        }
    }

    // 플레이어 재정 상태 재계산
    recalculatePlayerFinancials() {
        const player = this.gameState.player;
        if (!player) return;

        // 자산으로부터 얻는 수동 수입 계산 (일반 자산 + 주식 배당금)
        let passiveFromAssets = FinancialCalculator.calculatePassiveIncome(player.assets);
        
        // 주식 배당금 계산
        let stockDividends = 0;
        if (player.stocks && typeof player.stocks === 'object') {
            stockDividends = Object.values(player.stocks).reduce((total, stock) => {
                return total + (stock.monthlyDividend || 0);
            }, 0);
        }
        
        // 펀드 수익 계산
        let fundIncome = 0;
        if (player.funds && typeof player.funds === 'object') {
            fundIncome = Object.values(player.funds).reduce((total, fund) => {
                return total + (fund.monthlyDividend || 0);
            }, 0);
        }
        
        console.log('수동 소득 계산:', {
            일반자산: passiveFromAssets,
            주식배당금: stockDividends,
            펀드수익: fundIncome,
            총합: passiveFromAssets + stockDividends + fundIncome
        });
        
        player.passiveIncome = passiveFromAssets + stockDividends + fundIncome;
        
        // 총 수입 = 급여 + 수동 수입
        player.totalIncome = player.salary + player.passiveIncome;
        
        // 자녀 관련 지출 계산 (자녀가 있을 때만)
        const currentChildTotalExpenses = player.expenses.childrenCount * player.expenses.perChildExpense;
        player.expenses.children = currentChildTotalExpenses;
        
        // 총 지출 계산 - 기본 지출 + 자녀 지출만 (부채 상환액은 이미 기본 지출에 포함됨)
        const baseExpenses = (
            (player.expenses.taxes || 0) + 
            (player.expenses.homePayment || 0) + 
            (player.expenses.schoolLoan || 0) + 
            (player.expenses.carLoan || 0) + 
            (player.expenses.creditCard || 0) + 
            (player.expenses.retail || 0) + 
            (player.expenses.other || 0)
        );
        console.log('기본 지출:', baseExpenses, '자녀 지출:', currentChildTotalExpenses, '개별 지출:', player.expenses);
        
        // 총 지출 = 기본 지출 + 자녀 양육비 (부채 상환액은 기본 지출에 이미 포함되어 있음)
        player.totalExpenses = baseExpenses + currentChildTotalExpenses;
        
        // 새로 추가된 부채(투자로 인한 대출 등)의 상환액만 별도 계산
        const additionalDebtPayments = (player.liabilities && player.liabilities.length > 0) 
            ? player.liabilities
                .filter(debt => !debt.isInitial) // 초기 부채가 아닌 것만
                .reduce((sum, debt) => sum + (debt.monthlyPayment || 0), 0)
            : 0;
        
        // 추가 부채 상환액이 있으면 더함
        player.totalExpenses += additionalDebtPayments;
        
        console.log('총 지출:', player.totalExpenses, '추가 부채 상환액:', additionalDebtPayments);
        
        // 월 현금 흐름 = 총 수입 - 총 지출
        player.monthlyCashFlow = player.totalIncome - player.totalExpenses;
    }

    // 잘못 저장된 주식 데이터 정리 (중복 호출 안전)
    cleanupMisplacedStocks() {
        const player = this.gameState.player;
        if (!player) {
            console.log('주식 정리: 플레이어 데이터 없음');
            return;
        }
        
        // 이미 정리가 완료된 경우 스킵 (중복 호출 방지)
        if (player._stocksCleanedUp) {
            console.log('주식 데이터 정리 스킵: 이미 정리 완료됨');
            return;
        }
        
        console.log('주식 데이터 정리 시작');
        console.log('현재 stocks 객체:', player.stocks);
        console.log('현재 assets 배열 길이:', player.assets ? player.assets.length : 0);
        
        if (!player.assets) {
            console.log('주식 정리: assets 배열 없음');
            player._stocksCleanedUp = true; // 정리 완료 플래그 설정
            return;
        }
        
        // assets 배열에서 주식 찾기
        const stockAssets = [];
        const nonStockAssets = [];
        
        player.assets.forEach(asset => {
            if (asset.type === 'Stock' || asset.name.includes('주식')) {
                stockAssets.push(asset);
            } else {
                nonStockAssets.push(asset);
            }
        });
        
        console.log('assets 배열에서 발견된 주식:', stockAssets.length, '개');
        console.log('stocks 객체 키 개수:', player.stocks ? Object.keys(player.stocks).length : 0, '개');
        
        if (stockAssets.length > 0) {
            console.log('잘못 저장된 주식 발견:', stockAssets);
            
            // stocks 객체가 없으면 생성
            if (!player.stocks) {
                player.stocks = {};
            }
            
            // 주식들을 stocks 객체로 이동 (중복 방지를 위해 기존 데이터 보존)
            stockAssets.forEach(stockAsset => {
                // 주식 심볼 추출
                let symbol = this.getStockSymbolFromTitle(stockAsset.name);
                if (!symbol) {
                    const match = stockAsset.name.match(/([A-Z0-9]+)/);
                    symbol = match ? match[1] : 'UNKNOWN';
                }
                
                const cost = stockAsset.totalValue || stockAsset.totalInvested || 0;
                const monthlyIncome = stockAsset.monthlyIncome || 0;
                
                // stocks 객체에 기존 데이터가 없는 경우에만 추가
                if (!player.stocks[symbol]) {
                    player.stocks[symbol] = {
                        shares: 1, // 임시로 1주로 설정
                        totalInvested: cost,
                        averagePrice: cost,
                        monthlyDividend: monthlyIncome
                    };
                    console.log(`주식 ${symbol} 새로 추가:`, player.stocks[symbol]);
                } else {
                    // 기존 종목이 있는 경우 수량 증가하지 않고 로그만 출력
                    console.log(`주식 ${symbol} 이미 존재함, 수량 유지:`, player.stocks[symbol]);
                }
            });
            
            // assets 배열에서 주식 제거
            player.assets = nonStockAssets;
            
            console.log('주식 데이터 정리 완료');
        }
        
        // 정리 완료 플래그 설정 (중복 호출 방지)
        player._stocksCleanedUp = true;
    }

    // 주식 분할 처리
    handleStockSplit(stockSymbol, splitRatio) {
        const player = this.gameState.player;
        if (!player || !player.stocks) return false;

        // 해당 주식 보유 여부 확인
        if (!player.stocks[stockSymbol]) {
            return false; // 주식을 보유하지 않음
        }

        const stockInfo = player.stocks[stockSymbol];
        
        // 분할 비율 파싱 (예: "2 for 1" -> [2, 1])
        const [newShares, oldShares] = splitRatio.split(' for ').map(n => parseInt(n.trim()));
        
        // 주식 수량 증가
        const originalShares = stockInfo.shares;
        stockInfo.shares = Math.floor(originalShares * newShares / oldShares);
        
        // 평균 매입가 조정 (분할 비율만큼 감소)
        stockInfo.averagePrice = stockInfo.averagePrice * oldShares / newShares;
        
        // 총 투자금액은 변경 없음 (분할로 인한 가치 변화 없음)
        
        // 로그 추가
        this.addGameLog(`${stockSymbol} 주식 분할 (${splitRatio}): ${originalShares}주 → ${stockInfo.shares}주`);
        this.addGameLog(`평균 매입가: ${GameUtils.formatCurrency(stockInfo.averagePrice * newShares / oldShares)} → ${GameUtils.formatCurrency(stockInfo.averagePrice)}`);
        
        return true;
    }

    // 주식 병합 (역분할) 처리
    handleStockMerge(stockSymbol, mergeRatio) {
        const player = this.gameState.player;
        if (!player || !player.stocks) return false;

        // 해당 주식 보유 여부 확인
        if (!player.stocks[stockSymbol]) {
            return false; // 주식을 보유하지 않음
        }

        const stockInfo = player.stocks[stockSymbol];
        
        // 병합 비율 파싱 (예: "1 for 2" -> [1, 2])
        const [newShares, oldShares] = mergeRatio.split(' for ').map(n => parseInt(n.trim()));
        
        // 주식 수량 감소
        const originalShares = stockInfo.shares;
        stockInfo.shares = Math.floor(originalShares * newShares / oldShares);
        
        // 평균 매입가 조정 (병합 비율만큼 증가)
        stockInfo.averagePrice = stockInfo.averagePrice * oldShares / newShares;
        
        // 총 투자금액은 변경 없음 (병합으로 인한 가치 변화 없음)
        
        // 로그 추가
        this.addGameLog(`${stockSymbol} 주식 병합 (${mergeRatio}): ${originalShares}주 → ${stockInfo.shares}주`);
        this.addGameLog(`평균 매입가: ${GameUtils.formatCurrency(stockInfo.averagePrice * oldShares / newShares)} → ${GameUtils.formatCurrency(stockInfo.averagePrice)}`);
        
        return true;
    }

    // StockEvent 카드 처리
    handleStockEvent(card) {
        const player = this.gameState.player;
        if (!player) return;

        const assetDetails = card.assetDetails;
        const stockSymbol = this.extractStockSymbol(assetDetails.assetName);
        
        if (!stockSymbol) {
            this.showModalNotification("오류", "주식 종목을 찾을 수 없습니다.");
            return;
        }

        // 해당 주식 보유 여부 확인
        if (!player.stocks || !player.stocks[stockSymbol]) {
            this.showModalNotification("알림", `${stockSymbol} 주식을 보유하고 있지 않습니다.`);
            return;
        }

        let success = false;
        
        if (assetDetails.splitType === 'split') {
            // 주식 분할 처리
            success = this.handleStockSplit(stockSymbol, assetDetails.splitRatio);
        } else if (assetDetails.splitType === 'reverse_split') {
            // 주식 병합 처리
            success = this.handleStockMerge(stockSymbol, assetDetails.splitRatio);
        }

        if (success) {
            // UI 업데이트
            this.recalculatePlayerFinancials();
            this.updateUI();
            StorageManager.saveGameState(this.gameState);
            
            // 성공 메시지 표시
            const eventType = assetDetails.splitType === 'split' ? '분할' : '병합';
            this.showModalNotification(
                `${stockSymbol} 주식 ${eventType} 완료`,
                `${card.effectDescription}\n\n현재 보유 수량: ${player.stocks[stockSymbol].shares}주\n평균 매입가: ${GameUtils.formatCurrency(player.stocks[stockSymbol].averagePrice)}`
            );
        }
    }

    // 주식 종목 심볼 추출 (카드 이름에서)
    extractStockSymbol(assetName) {
        // 카드 이름에서 주식 심볼 추출
        if (assetName.includes('OK4U')) return 'OK4U';
        if (assetName.includes('MYT4U')) return 'MYT4U';
        if (assetName.includes('ON2U')) return 'ON2U';
        return null;
    }

    // =================================================================
    // 파산 처리 시스템
    // =================================================================

    // 총 자산 가치 계산
    calculateTotalAssetValue() {
        const player = this.gameState.player;
        let totalValue = 0;

        // 부동산 자산
        if (player.assets && player.assets.length > 0) {
            totalValue += player.assets.reduce((sum, asset) => {
                const currentValue = asset.currentValue || asset.totalInvested || asset.totalValue || 0;
                return sum + parseFloat(currentValue);
            }, 0);
        }

        // 주식 자산
        if (player.stocks) {
            Object.values(player.stocks).forEach(stock => {
                const shares = parseFloat(stock.shares || 0);
                const currentPrice = parseFloat(stock.averagePrice || 0);
                totalValue += shares * currentPrice;
            });
        }

        // 펀드 자산
        if (player.funds) {
            Object.values(player.funds).forEach(fund => {
                const shares = parseFloat(fund.shares || 0);
                const currentPrice = parseFloat(fund.averagePrice || 0);
                totalValue += shares * currentPrice;
            });
        }

        // 기타 투자 자산 (금, 은 등)
        if (player.investments) {
            totalValue += Object.values(player.investments).reduce((sum, investment) => {
                return sum + parseFloat(investment.currentValue || investment.totalInvested || 0);
            }, 0);
        }

        return totalValue;
    }

    // 파산 처리 메인 로직
    handleBankruptcy() {
        const player = this.gameState.player;
        const totalAssetValue = this.calculateTotalAssetValue();
        const currentCash = parseFloat(player.cash || 0);
        const shortfall = Math.abs(currentCash); // 부족한 현금 (양수)

        console.log('파산 처리 시작:', {
            currentCash: currentCash,
            shortfall: shortfall,
            totalAssetValue: totalAssetValue
        });

        // 자산이 있으면 강제 판매 시도
        if (totalAssetValue > 0) {
            const saleResult = this.forceAssetSale(shortfall);
            
            if (saleResult.success) {
                // 자산 판매로 위기 극복
                this.addGameLog(`긴급 상황: 자산을 판매하여 ${GameUtils.formatCurrency(saleResult.totalSold)}를 확보했습니다.`, 'warning');
                
                setTimeout(() => {
                    this.showModalNotification(
                        "긴급 자산 판매!",
                        `현금 부족으로 인해 자산을 강제로 판매했습니다.\n\n판매 금액: ${GameUtils.formatCurrency(saleResult.totalSold)}\n현재 현금: ${GameUtils.formatCurrency(player.cash)}\n\n⚠️ 앞으로 재정 관리에 더욱 주의하세요!`
                    );
                }, 500);
                
                return { isBankrupt: false, forced: true };
            }
        }

        // 자산도 없거나 판매해도 부족한 경우 → 파산
        return this.declareBankruptcy();
    }

    // 자산 강제 판매
    forceAssetSale(targetAmount) {
        const player = this.gameState.player;
        let totalSold = 0;
        const soldAssets = [];

        // 1. 주식부터 판매 (가장 유동성이 높음)
        if (player.stocks && totalSold < targetAmount) {
            const stockEntries = Object.entries(player.stocks);
            for (const [symbol, stock] of stockEntries) {
                if (totalSold >= targetAmount) break;
                
                const shares = parseFloat(stock.shares || 0);
                const price = parseFloat(stock.averagePrice || 0);
                const value = shares * price;
                
                if (value > 0) {
                    totalSold += value;
                    soldAssets.push(`${symbol} 주식 ${shares}주`);
                    delete player.stocks[symbol];
                    
                    this.addGameLog(`강제 판매: ${symbol} 주식 ${shares}주를 ${GameUtils.formatCurrency(value)}에 판매했습니다.`, 'warning');
                }
            }
        }

        // 2. 펀드 판매
        if (player.funds && totalSold < targetAmount) {
            const fundEntries = Object.entries(player.funds);
            for (const [symbol, fund] of fundEntries) {
                if (totalSold >= targetAmount) break;
                
                const shares = parseFloat(fund.shares || 0);
                const price = parseFloat(fund.averagePrice || 0);
                const value = shares * price;
                
                if (value > 0) {
                    totalSold += value;
                    soldAssets.push(`${symbol} 펀드 ${shares}주`);
                    delete player.funds[symbol];
                    
                    this.addGameLog(`강제 판매: ${symbol} 펀드 ${shares}주를 ${GameUtils.formatCurrency(value)}에 판매했습니다.`, 'warning');
                }
            }
        }

        // 3. 부동산 자산 판매
        if (player.assets && player.assets.length > 0 && totalSold < targetAmount) {
            const assetsToSell = [...player.assets]; // 복사본 생성
            
            for (let i = assetsToSell.length - 1; i >= 0; i--) {
                if (totalSold >= targetAmount) break;
                
                const asset = assetsToSell[i];
                const value = parseFloat(asset.currentValue || asset.totalInvested || asset.totalValue || 0);
                
                if (value > 0) {
                    totalSold += value;
                    soldAssets.push(asset.name || '부동산');
                    player.assets.splice(i, 1);
                    
                    this.addGameLog(`강제 판매: ${asset.name || '부동산'}을 ${GameUtils.formatCurrency(value)}에 판매했습니다.`, 'warning');
                }
            }
        }

        // 4. 기타 투자 자산 판매
        if (player.investments && totalSold < targetAmount) {
            const investmentEntries = Object.entries(player.investments);
            for (const [name, investment] of investmentEntries) {
                if (totalSold >= targetAmount) break;
                
                const value = parseFloat(investment.currentValue || investment.totalInvested || 0);
                
                if (value > 0) {
                    totalSold += value;
                    soldAssets.push(name);
                    delete player.investments[name];
                    
                    this.addGameLog(`강제 판매: ${name}을 ${GameUtils.formatCurrency(value)}에 판매했습니다.`, 'warning');
                }
            }
        }

        // 현금에 판매 금액 추가
        player.cash = parseFloat(player.cash || 0) + totalSold;

        // 재정 상태 재계산
        this.recalculatePlayerFinancials();

        return {
            success: totalSold >= targetAmount,
            totalSold: totalSold,
            soldAssets: soldAssets,
            stillShort: Math.max(0, targetAmount - totalSold)
        };
    }

    // 파산 선언 및 게임 종료
    declareBankruptcy() {
        const player = this.gameState.player;
        
        // 파산 상태 설정
        this.gameState.gameEnded = true;
        this.gameState.endReason = 'bankruptcy';
        this.gameState.endDate = new Date().toISOString();
        
        // 파산 로그 추가
        this.addGameLog("파산: 현금과 자산이 모두 부족하여 게임이 종료되었습니다.", 'error');
        
        // 게임 상태 저장
        this.updateUI();
        StorageManager.saveGameState(this.gameState);
        
        // 파산 알림 표시
        setTimeout(() => {
            this.showModalNotification(
                "🚨 파산 선언",
                `게임이 종료되었습니다.\n\n현금: ${GameUtils.formatCurrency(player.cash)}\n총 자산 가치: ${GameUtils.formatCurrency(this.calculateTotalAssetValue())}\n\n현금흐름이 마이너스이고 현금과 자산이 모두 부족하여 더 이상 게임을 계속할 수 없습니다.\n\n💡 다시 도전해보세요!`,
                () => {
                    // 게임 재시작 옵션 제공
                    this.showRestartOptions();
                }
            );
        }, 500);
        
        return { isBankrupt: true };
    }

    // 게임 재시작 옵션 표시
    showRestartOptions() {
        setTimeout(() => {
            this.showModalNotification(
                "게임 재시작",
                "새 게임을 시작하시겠습니까?",
                () => {
                    // 게임 재시작
                    window.location.reload();
                },
                true // 취소 버튼 표시
            );
        }, 1000);
    }
}