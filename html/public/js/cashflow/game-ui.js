// UI 렌더링 및 업데이트 함수들

// CashflowGame 클래스에 UI 관련 메서드들 추가
Object.assign(CashflowGame.prototype, {
    
    // 직업 목록 렌더링
    renderProfessionsList() {
        const professionList = document.getElementById('profession-list');
        professionList.innerHTML = '';
        PROFESSION_DATA.forEach((profession, index) => {
            const card = this.createProfessionCard(profession, index);
            professionList.appendChild(card);
        });
    },

    // 직업 카드 생성
    createProfessionCard(profession, index) {
        const card = document.createElement('div');
        card.className = 'profession-card border-2 border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition-all cursor-pointer';
        card.dataset.professionIndex = index;
        
        // 월별 수입/지출 요약
        const monthlyIncome = profession.incomeStatement.salary + (profession.incomeStatement.passiveIncome || 0);
        const monthlyExpenses = profession.expenses.totalExpenses;
        const cashFlow = profession.financialSummary.monthlyCashFlow;
        
        card.innerHTML = `
            <div class="flex flex-col">
                <!-- 헤더 영역: 직업명과 현금흐름 -->
                <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-200">
                    <h4 class="font-bold text-lg text-gray-800">${profession.jobTitle} (${profession.originalTitle})</h4>
                    <div class="flex flex-col items-end">
                        <div class="text-xs text-gray-500">월 현금흐름</div>
                        <div class="text-base font-bold ${cashFlow >= 0 ? 'text-green-600' : 'text-red-600'}">
                            ${GameUtils.formatCurrency(cashFlow)}
                        </div>
                    </div>
                </div>
                
                <!-- 재무 요약 영역 -->
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-blue-50 p-2 rounded-md">
                        <div class="text-xs text-gray-600 mb-1">월 수입</div>
                        <div class="text-sm font-semibold text-blue-700">${GameUtils.formatCurrency(monthlyIncome)}</div>
                    </div>
                    <div class="bg-red-50 p-2 rounded-md">
                        <div class="text-xs text-gray-600 mb-1">월 지출</div>
                        <div class="text-sm font-semibold text-red-700">${GameUtils.formatCurrency(monthlyExpenses)}</div>
                    </div>
                    <div class="bg-green-50 p-2 rounded-md">
                        <div class="text-xs text-gray-600 mb-1">시작 현금</div>
                        <div class="text-sm font-semibold text-green-700">${GameUtils.formatCurrency(profession.balanceSheet.assets.cash)}</div>
                    </div>
                    <div class="bg-purple-50 p-2 rounded-md">
                        <div class="text-xs text-gray-600 mb-1">월급 금액</div>
                        <div class="text-sm font-semibold text-purple-700">${GameUtils.formatCurrency(profession.financialSummary.payDayAmount)}</div>
                    </div>
                </div>
                
                <!-- 지출 항목 자세히보기 버튼 -->
                <button class="view-expenses-btn w-full text-sm text-blue-600 border border-blue-300 rounded-md py-1.5 hover:bg-blue-50 transition-colors">
                    지출 항목 자세히보기
                </button>
                
                <!-- 지출 세부 항목 (초기에는 숨김) -->
                <div class="expenses-details hidden mt-3 pt-3 border-t border-gray-200">
                    <h5 class="text-sm font-semibold text-gray-700 mb-2">월별 지출 세부 항목</h5>
                    <div class="grid grid-cols-1 gap-1 text-xs">
                        <div class="flex justify-between">
                            <span>세금:</span>
                            <span class="font-medium">${GameUtils.formatCurrency(profession.expenses.taxes)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>주택 대출:</span>
                            <span class="font-medium">${GameUtils.formatCurrency(profession.expenses.homeMortgagePayment)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>학자금 대출:</span>
                            <span class="font-medium">${GameUtils.formatCurrency(profession.expenses.schoolLoanPayment)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>자동차 대출:</span>
                            <span class="font-medium">${GameUtils.formatCurrency(profession.expenses.carLoanPayment)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>신용카드:</span>
                            <span class="font-medium">${GameUtils.formatCurrency(profession.expenses.creditCardPayment)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>소매 구매:</span>
                            <span class="font-medium">${GameUtils.formatCurrency(profession.expenses.retailExpenses)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>기타 지출:</span>
                            <span class="font-medium">${GameUtils.formatCurrency(profession.expenses.otherExpenses)}</span>
                        </div>
                        ${profession.expenses.numberOfChildren > 0 ? 
                        `<div class="flex justify-between">
                            <span>자녀 양육비 (${profession.expenses.numberOfChildren}명):</span>
                            <span class="font-medium">${GameUtils.formatCurrency(profession.expenses.childExpensesPerChild * profession.expenses.numberOfChildren)}</span>
                        </div>` : ''}
                        <div class="flex justify-between pt-1 mt-1 border-t border-gray-200 font-semibold text-red-700">
                            <span>총 지출:</span>
                            <span>${GameUtils.formatCurrency(profession.expenses.totalExpenses)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        card.addEventListener('click', (e) => {
            // 지출 자세히보기 버튼이 클릭된 경우, 이벤트 전파 중지
            if (e.target.classList.contains('view-expenses-btn') || e.target.closest('.view-expenses-btn')) {
                e.stopPropagation();
                const detailsEl = card.querySelector('.expenses-details');
                detailsEl.classList.toggle('hidden');
                const btn = card.querySelector('.view-expenses-btn');
                btn.textContent = detailsEl.classList.contains('hidden') ? 
                    '지출 항목 자세히보기' : '접기';
                return;
            }
            
            // 일반 카드 클릭 시 직업 선택
            this.selectProfession(index);
        });
        
        return card;
    },

    // 직업 선택
    selectProfession(index) {
        document.querySelectorAll('.profession-card').forEach(card => {
            card.classList.remove('selected', 'border-blue-500', 'bg-blue-50');
            card.classList.add('border-gray-200');
        });
        const selectedCard = document.querySelector(`[data-profession-index="${index}"]`);
        selectedCard.classList.add('selected', 'border-blue-500', 'bg-blue-50');
        selectedCard.classList.remove('border-gray-200');
        const startBtn = document.getElementById('start-game-btn');
        startBtn.dataset.selectedProfession = index;
        this.updateStartButtonState();
    },

    // 시작 버튼 상태 업데이트
    updateStartButtonState() {
        const playerName = document.getElementById('player-name').value.trim();
        const startBtn = document.getElementById('start-game-btn');
        const hasSelection = startBtn.dataset.selectedProfession !== undefined;
        
        if (playerName && hasSelection) {
            startBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            startBtn.disabled = false;
        } else {
            startBtn.classList.add('opacity-50', 'cursor-not-allowed');
            startBtn.disabled = true;
        }
    },

    // 게임 UI로 전환
    switchToGameUI() {
        console.log('=== switchToGameUI 호출됨 ===');
        
        // 모든 게임 시작 관련 요소들을 강제로 숨기기
        const elementsToHide = [
            'profession-selection',
            'start-game-fixed-button',
            'game-intro',
            'intro-screen'
        ];
        
        elementsToHide.forEach(elementId => {
            const element = document.getElementById(elementId);
            if (element) {
                element.style.display = 'none';
                element.classList.add('hidden');
                element.style.visibility = 'hidden';
                console.log(`${elementId} 요소를 숨겼습니다`);
            }
        });
        
        // 직업 선택 화면 완전히 숨기기 (추가 확인)
        const professionSelection = document.getElementById('profession-selection');
        console.log('profession-selection 요소:', professionSelection);
        if (professionSelection) {
            professionSelection.style.display = 'none !important';
            professionSelection.classList.add('hidden');
            professionSelection.style.visibility = 'hidden';
            professionSelection.setAttribute('hidden', 'true');
            console.log('직업 선택 화면을 완전히 숨겼습니다');
        } else {
            console.warn('profession-selection 요소를 찾을 수 없습니다');
        }
        
        // 모든 탭 콘텐츠 숨기기
        const allTabContents = document.querySelectorAll('.tab-content');
        console.log('모든 탭 콘텐츠 개수:', allTabContents.length);
        allTabContents.forEach((tab, index) => {
            if (tab.id !== 'tab-content-dashboard') {
                tab.style.display = 'none';
                tab.classList.add('hidden');
                console.log(`탭 ${index} 숨김:`, tab.id);
            }
        });
        
        // 하단 네비게이션 보이기
        const bottomNav = document.getElementById('bottom-nav');
        console.log('bottom-nav 요소:', bottomNav);
        if (bottomNav) {
            bottomNav.style.display = 'block';
            bottomNav.classList.remove('hidden');
            bottomNav.style.visibility = 'visible';
            console.log('하단 네비게이션을 표시했습니다');
        } else {
            console.warn('bottom-nav 요소를 찾을 수 없습니다');
        }
        
        // 대시보드 탭만 보이기
        const dashboardTab = document.getElementById('tab-content-dashboard');
        console.log('tab-content-dashboard 요소:', dashboardTab);
        if (dashboardTab) {
            dashboardTab.style.display = 'block';
            dashboardTab.classList.remove('hidden');
            dashboardTab.style.visibility = 'visible';
            console.log('대시보드 탭을 표시했습니다');
        } else {
            console.warn('tab-content-dashboard 요소를 찾을 수 없습니다');
        }
        
        // 네비게이션 버튼 상태 초기화 - 대시보드 활성화
        const navButtons = document.querySelectorAll('.nav-btn');
        console.log('네비게이션 버튼 개수:', navButtons.length);
        navButtons.forEach(btn => {
            btn.classList.remove('text-blue-600', 'bg-blue-50');
        });
        
        const dashboardButton = document.querySelector('[data-tab="dashboard"]');
        console.log('대시보드 버튼:', dashboardButton);
        if (dashboardButton) {
            dashboardButton.classList.add('text-blue-600', 'bg-blue-50');
            console.log('대시보드 버튼을 활성화했습니다');
        }
        
        // DOM이 완전히 업데이트되도록 강제로 리플로우 트리거
        document.body.offsetHeight;
        
        console.log('=== switchToGameUI 완료 ===');
    },

    // UI 업데이트
    updateUI() {
        this.updatePlayerInfo();
        this.updateAssetsAndLiabilities();
        this.updateGameLogUI();
        this.updateCashflowChart();
        this.updateCashflowGaugeChart();
        this.checkWinCondition();
    },

    // 플레이어 정보 업데이트
    updatePlayerInfo() {
        const player = this.gameState.player;
        if (!player) return;

        // DOM 요소 안전하게 업데이트
        const updateElement = (id, value) => {
            const element = document.getElementById(id);
            if (element) element.textContent = value;
        };

        updateElement('player-name-display', player.name);
        updateElement('player-profession', player.profession);
        updateElement('player-dream', player.dream);
        updateElement('dream-cost-display', GameUtils.formatCurrency(player.dreamCost));
        // 현금 표시 (파산 상태 체크)
        const cashElement = document.getElementById('current-cash');
        if (cashElement) {
            if (this.gameState.gameEnded && this.gameState.endReason === 'bankruptcy') {
                cashElement.innerHTML = `${GameUtils.formatCurrency(player.cash)} <span class="text-red-600 font-bold">(파산)</span>`;
            } else {
                cashElement.textContent = GameUtils.formatCurrency(player.cash);
            }
        }
        
        updateElement('total-income', GameUtils.formatCurrency(player.totalIncome));
        updateElement('total-expenses', GameUtils.formatCurrency(player.totalExpenses));
        
        // 현금흐름 표시 (파산 상태 체크)
        const cashflowElement = document.getElementById('monthly-cashflow');
        if (cashflowElement) {
            if (this.gameState.gameEnded && this.gameState.endReason === 'bankruptcy') {
                cashflowElement.innerHTML = `${GameUtils.formatCurrency(player.monthlyCashFlow)} <span class="text-red-600 font-bold">(게임 종료)</span>`;
            } else {
                cashflowElement.textContent = GameUtils.formatCurrency(player.monthlyCashFlow);
            }
        }
        updateElement('passive-income', GameUtils.formatCurrency(player.passiveIncome));
        updateElement('children-count', player.expenses.childrenCount);
        updateElement('children-expenses', GameUtils.formatCurrency(player.expenses.children || 0));
        
        // 자녀 버튼 상태 업데이트
        this.updateChildButtonState();
    },

    // 자산 및 부채 목록 업데이트
    updateAssetsAndLiabilities() {
        this.updateAssetsList();
        this.updateLiabilitiesList();
        this.updateStocksList();
        this.updateEmergencyLoansList();
        this.updateDreamInfo();
        this.updateAssetsPage();
        this.updateFinancialReport();
    },

    // 자산 목록 업데이트
    updateAssetsList() {
        const assetsList = document.getElementById('assets-list');
        const player = this.gameState.player;

        if (!assetsList) return; // DOM 요소가 없으면 종료
        if (!player || !player.assets || player.assets.length === 0) {
            assetsList.innerHTML = '<div class="text-gray-500 text-sm">보유 자산이 없습니다.</div>';
            return;
        }

        assetsList.innerHTML = '';
        player.assets.forEach((asset, index) => {
            const assetElement = document.createElement('div');
            assetElement.className = 'flex justify-between items-center p-3 bg-gray-50 rounded border mb-2';
            
            const leftDiv = document.createElement('div');
            const rightDiv = document.createElement('div');
            rightDiv.className = 'text-right';
            
            // 자산 정보 표시
            leftDiv.innerHTML = `
                <div class="font-medium text-sm">${asset.name}</div>
                <div class="text-xs text-gray-600">월 현금흐름: ${GameUtils.formatCurrency(asset.monthlyIncome)}</div>
            `;
            
            // 판매 버튼과 가치 표시
            rightDiv.innerHTML = `
                <div class="text-sm font-medium mb-1">${GameUtils.formatCurrency(asset.totalValue || asset.downPayment || 0)}</div>
                <button class="sell-asset-btn text-xs bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" 
                        data-asset-index="${index}">판매</button>
            `;
            
            assetElement.appendChild(leftDiv);
            assetElement.appendChild(rightDiv);
            assetsList.appendChild(assetElement);
        });
    },

    // 부채 목록 업데이트
    updateLiabilitiesList() {
        const liabilitiesList = document.getElementById('liabilities-list');
        const player = this.gameState.player;

        if (!liabilitiesList) return; // DOM 요소가 없으면 종료
        if (!player || !player.liabilities || player.liabilities.length === 0) {
            liabilitiesList.innerHTML = '<div class="text-gray-500 text-sm">부채가 없습니다.</div>';
            return;
        }

        liabilitiesList.innerHTML = '';
        player.liabilities.forEach(liability => {
            const liabilityElement = document.createElement('div');
            liabilityElement.className = 'flex justify-between items-center p-3 bg-red-50 rounded border mb-2';
            
            liabilityElement.innerHTML = `
                <div>
                    <div class="font-medium text-sm">${liability.name}</div>
                    <div class="text-xs text-gray-600">월 상환액: ${GameUtils.formatCurrency(liability.monthlyPayment)}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium">${GameUtils.formatCurrency(liability.amount)}</div>
                </div>
            `;
            
            liabilitiesList.appendChild(liabilityElement);
        });
    },

    // 주식 목록 업데이트
    updateStocksList() {
        const stocksList = document.getElementById('stocks-list');
        const player = this.gameState.player;

        if (!stocksList) return; // DOM 요소가 없으면 종료
        if (!player || !player.stocks || Object.keys(player.stocks).length === 0) {
            stocksList.innerHTML = '<div class="text-gray-500 text-sm">보유 주식이 없습니다.</div>';
            return;
        }

        stocksList.innerHTML = '';
        Object.entries(player.stocks).forEach(([symbol, data]) => {
            const stockElement = document.createElement('div');
            stockElement.className = 'flex justify-between items-center p-3 bg-blue-50 rounded border mb-2';
            
            const totalValue = data.shares * data.averagePrice;
            
            stockElement.innerHTML = `
                <div>
                    <div class="font-medium text-sm">${symbol}</div>
                    <div class="text-xs text-gray-600">${data.shares}주 보유 (평균 ${GameUtils.formatCurrency(data.averagePrice)})</div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium">${GameUtils.formatCurrency(totalValue)}</div>
                    <button class="sell-stock-btn text-xs bg-orange-500 text-white px-2 py-1 rounded hover:bg-orange-600" 
                            data-stock-symbol="${symbol}">판매</button>
                </div>
            `;
            
            stocksList.appendChild(stockElement);
        });
    },

    // 긴급 대출 목록 업데이트
    updateEmergencyLoansList() {
        const loansList = document.getElementById('emergency-loans-list');
        const player = this.gameState.player;

        if (!loansList) return; // DOM 요소가 없으면 종료
        if (!player || !player.emergencyLoans || player.emergencyLoans.length === 0) {
            loansList.innerHTML = '<div class="text-gray-500 text-sm">긴급 대출이 없습니다.</div>';
            return;
        }

        loansList.innerHTML = '';
        player.emergencyLoans.forEach(loan => {
            const loanElement = document.createElement('div');
            loanElement.className = 'flex justify-between items-center p-3 bg-yellow-50 rounded border mb-2';
            
            loanElement.innerHTML = `
                <div>
                    <div class="font-medium text-sm">긴급 대출</div>
                    <div class="text-xs text-gray-600">월 이자: ${GameUtils.formatCurrency(loan.monthlyPayment)}</div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium">${GameUtils.formatCurrency(loan.remainingAmount)}</div>
                    <button class="repay-loan-btn text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600" 
                            data-loan-id="${loan.id}">상환</button>
                </div>
            `;
            
            loansList.appendChild(loanElement);
        });
    },

    // 게임 로그 UI 업데이트
    updateGameLogUI() {
        const player = this.gameState.player;
        
        if (!player || !this.gameState.gameLog) return;

        // 대시보드 최근 활동 업데이트
        this.updateDashboardLog();
        
        // 전체 게임 로그 업데이트
        this.updateFullGameLog();
    },

    // 대시보드 최근 활동 업데이트
    updateDashboardLog() {
        const dashboardLogContainer = document.getElementById('dashboard-game-log');
        
        if (!dashboardLogContainer) return;

        dashboardLogContainer.innerHTML = '';
        
        if (!this.gameState.gameLog || this.gameState.gameLog.length === 0) {
            dashboardLogContainer.innerHTML = '<div class="text-sm text-gray-500 text-center py-4">게임 활동이 여기에 표시됩니다.</div>';
            return;
        }

        // 최근 5개 로그만 표시
        this.gameState.gameLog.slice(0, 5).forEach(entry => {
            const logEntry = document.createElement('div');
            logEntry.className = `p-2 rounded mb-1 text-sm ${
                entry.type === 'error' || entry.type === 'event-negative' ? 'bg-red-50 text-red-700 border border-red-200' :
                entry.type === 'success' || entry.type === 'income' || entry.type === 'event-positive' ? 'bg-green-50 text-green-700 border border-green-200' :
                entry.type === 'expense' || entry.type === 'expense-detail' ? 'bg-orange-50 text-orange-700 border border-orange-200' :
                'bg-blue-50 text-blue-700 border border-blue-200'
            }`;
            
            const timestamp = new Date(entry.timestamp).toLocaleTimeString('ko-KR', { hour: '2-digit', minute: '2-digit' });
            
            logEntry.innerHTML = `
                <div class="flex justify-between items-start">
                    <span class="flex-1">${entry.message}</span>
                    <span class="text-xs opacity-75 ml-2">${timestamp}</span>
                </div>
            `;
            
            dashboardLogContainer.appendChild(logEntry);
        });
    },

    // 전체 게임 로그 업데이트
    updateFullGameLog() {
        const fullLogContainer = document.getElementById('full-game-log');
        
        if (!fullLogContainer) return;

        fullLogContainer.innerHTML = '';
        
        if (!this.gameState.gameLog || this.gameState.gameLog.length === 0) {
            fullLogContainer.innerHTML = '<div class="text-sm text-gray-500 text-center py-4">게임 로그가 여기에 표시됩니다.</div>';
            return;
        }

        // 모든 로그 표시
        this.gameState.gameLog.forEach(entry => {
            const logEntry = document.createElement('div');
            logEntry.className = `p-3 rounded-lg mb-2 text-sm ${
                entry.type === 'error' || entry.type === 'event-negative' ? 'bg-red-50 text-red-800 border border-red-200' :
                entry.type === 'success' || entry.type === 'income' || entry.type === 'event-positive' ? 'bg-green-50 text-green-800 border border-green-200' :
                entry.type === 'expense' || entry.type === 'expense-detail' ? 'bg-orange-50 text-orange-800 border border-orange-200' :
                'bg-blue-50 text-blue-800 border border-blue-200'
            }`;
            
            const timestamp = new Date(entry.timestamp).toLocaleString('ko-KR');
            
            let content = `
                <div class="flex justify-between items-start mb-1">
                    <span class="flex-1 font-medium">${entry.message}</span>
                    <span class="text-xs opacity-75 ml-2">${timestamp}</span>
                </div>
            `;
            
            // 세부 내역이 있으면 표시
            if (entry.details && Array.isArray(entry.details) && entry.details.length > 0) {
                content += `
                    <div class="mt-2 pl-3 border-l-2 border-gray-300">
                        <div class="text-xs space-y-1">
                            ${entry.details.map(detail => `<div>• ${detail}</div>`).join('')}
                        </div>
                    </div>
                `;
            }
            
            logEntry.innerHTML = content;
            fullLogContainer.appendChild(logEntry);
        });
    },

    // 부채 상환 모달 표시
    showPayDebtModal(debtId) {
        const player = this.gameState.player;
        if (!player) return;

        const debt = player.liabilities.find(d => d.id === debtId);
        if (!debt) {
            this.showModalNotification("오류", "부채를 찾을 수 없습니다.");
            return;
        }

        const remainingAmount = debt.remainingAmount || 0;
        
        if (player.cash < remainingAmount) {
            this.showModalNotification(
                "현금 부족",
                `${debt.name} 상환을 위해서는 ${GameUtils.formatCurrency(remainingAmount)}이 필요합니다.\n\n현재 현금: ${GameUtils.formatCurrency(player.cash)}\n부족 금액: ${GameUtils.formatCurrency(remainingAmount - player.cash)}\n\n현금을 더 확보한 후 다시 시도하세요.`
            );
            return;
        }

        this.showModalNotification(
            "부채 상환",
            `${debt.name}을 상환하시겠습니까?\n\n상환 금액: ${GameUtils.formatCurrency(remainingAmount)}\n월 상환액 절약: ${GameUtils.formatCurrency(debt.monthlyPayment || 0)}\n\n상환 후 남은 현금: ${GameUtils.formatCurrency(player.cash - remainingAmount)}`,
            () => this.processDebtPayment(debtId),
            true // 취소 버튼 표시
        );
    },

    // 부채 상환 처리
    processDebtPayment(debtId) {
        const player = this.gameState.player;
        if (!player) return;

        const debtIndex = player.liabilities.findIndex(d => d.id === debtId);
        if (debtIndex === -1) {
            this.showModalNotification("오류", "부채를 찾을 수 없습니다.");
            return;
        }

        const debt = player.liabilities[debtIndex];
        const payoffAmount = debt.remainingAmount || 0;

        if (player.cash < payoffAmount) {
            this.showModalNotification("오류", "현금이 부족합니다.");
            return;
        }

        // 현금에서 상환 금액 차감
        player.cash -= payoffAmount;

        // 게임 로그 추가
        this.addGameLog(`${debt.name} 상환 완료: ${GameUtils.formatCurrency(payoffAmount)}`, 'income');
        this.addGameLog(`월 상환액 ${GameUtils.formatCurrency(debt.monthlyPayment || 0)} 절약`, 'income');

        // 초기 부채인 경우 해당 지출 항목도 0으로 설정
        if (debt.isInitial) {
            switch (debt.type) {
                case 'Mortgage':
                    player.expenses.homePayment = 0;
                    break;
                case 'StudentLoan':
                    player.expenses.schoolLoan = 0;
                    break;
                case 'CarLoan':
                    player.expenses.carLoan = 0;
                    break;
                case 'CreditCard':
                    player.expenses.creditCard = 0;
                    break;
                case 'Retail':
                    player.expenses.retail = 0;
                    break;
            }
        }

        // 부채 목록에서 제거
        player.liabilities.splice(debtIndex, 1);

        // 재무 상태 재계산
        this.recalculatePlayerFinancials();

        // UI 업데이트
        this.updateUI();
        StorageManager.saveGameState(this.gameState);

        // 완료 메시지
        setTimeout(() => {
            this.showModalNotification(
                "상환 완료! 🎉",
                `${debt.name} 상환이 완료되었습니다!\n\n월 지출이 ${GameUtils.formatCurrency(debt.monthlyPayment || 0)} 감소했습니다.\n\n현재 현금: ${GameUtils.formatCurrency(player.cash)}`
            );
        }, 200);
    },

    // 현금흐름 차트 업데이트
    updateCashflowChart() {
        const chartContainer = document.getElementById('cashflow-chart');
        const player = this.gameState.player;
        
        if (!chartContainer || !player) return;

        if (this.cashflowChart) {
            this.cashflowChart.dispose();
        }

        this.cashflowChart = echarts.init(chartContainer);
        
        const option = {
            title: {
                text: '월별 현금흐름',
                left: 'center',
                textStyle: {
                    fontSize: 14
                }
            },
            series: [{
                type: 'gauge',
                min: -5000,
                max: 5000,
                splitNumber: 10,
                axisLine: {
                    lineStyle: {
                        width: 6,
                        color: [
                            [0.3, '#fd666d'],
                            [0.7, '#ffc107'],
                            [1, '#37d67a']
                        ]
                    }
                },
                pointer: {
                    itemStyle: {
                        color: 'auto'
                    }
                },
                axisTick: {
                    distance: -30,
                    length: 8,
                    lineStyle: {
                        color: '#fff',
                        width: 2
                    }
                },
                splitLine: {
                    distance: -30,
                    length: 30,
                    lineStyle: {
                        color: '#fff',
                        width: 4
                    }
                },
                axisLabel: {
                    color: 'auto',
                    distance: 40,
                    fontSize: 10
                },
                detail: {
                    valueAnimation: true,
                    formatter: '{value}',
                    color: 'auto',
                    fontSize: 12
                },
                data: [{
                    value: player.monthlyCashFlow,
                    name: '현금흐름 ($)'
                }]
            }]
        };

        this.cashflowChart.setOption(option);
    },

    // 캐시플로우 게이지 차트 업데이트 (경주 탈출 진행도)
    updateCashflowGaugeChart() {
        const player = this.gameState.player;
        if (!player) return;
        
        const chartDom = document.getElementById('cashflow-gauge-chart');
        if (!chartDom) return;
        
        // 차트 초기화 (기존 차트가 있으면 제거 후 재생성)
        if (this.ratRaceChart) {
            this.ratRaceChart.dispose();
        }
        this.ratRaceChart = echarts.init(chartDom);
        
        // 경주 탈출 진행도 계산 (백분율)
        const passiveIncome = player.passiveIncome || 0;
        const totalExpenses = player.totalExpenses || 0;
        const progressPercentage = totalExpenses > 0 ? Math.min(100, (passiveIncome / totalExpenses) * 100) : 0;
        
        // 차트 옵션 설정 - 가로 막대 차트
        const option = {
            grid: {
                top: '10%',
                bottom: '30%',
                left: '3%',
                right: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value',
                max: 100,
                axisLabel: {
                    formatter: '{value}%',
                    fontSize: 10
                },
                splitLine: {
                    show: false
                }
            },
            yAxis: {
                type: 'category',
                data: [''],
                axisLabel: {
                    show: false
                },
                axisTick: {
                    show: false
                },
                axisLine: {
                    show: false
                }
            },
            series: [
                {
                    type: 'bar',
                    data: [progressPercentage],
                    barWidth: '40%',
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            { offset: 0, color: '#7cffb2' },
                            { offset: Math.min(progressPercentage / 100, 1), color: '#7cffb2' },
                            { offset: Math.min(progressPercentage / 100, 1), color: '#fddd60' },
                            { offset: 1, color: '#fddd60' }
                        ]),
                        borderRadius: [0, 4, 4, 0]
                    },
                    label: {
                        show: true,
                        position: 'right',
                        formatter: `${progressPercentage.toFixed(1)}%`,
                        fontSize: 12,
                        color: progressPercentage >= 100 ? '#10b981' : '#666'
                    },
                    z: 10
                }
            ],
            tooltip: {
                trigger: 'item',
                formatter: function() {
                    return `자산소득: ${GameUtils.formatCurrency(passiveIncome)}<br>총지출: ${GameUtils.formatCurrency(totalExpenses)}<br>진행률: ${progressPercentage.toFixed(1)}%`;
                }
            }
        };
        
        // 차트 하단에 설명 추가
        chartDom.parentNode.querySelector('.chart-description')?.remove();
        const descriptionEl = document.createElement('div');
        descriptionEl.className = 'chart-description text-xs text-center text-gray-500 mt-1';
        descriptionEl.textContent = `자산소득 ${GameUtils.formatCurrency(passiveIncome)} / 총지출 ${GameUtils.formatCurrency(totalExpenses)}`;
        chartDom.parentNode.appendChild(descriptionEl);
        
        // 차트 업데이트
        this.ratRaceChart.setOption(option);
    },

    // 승리 조건 확인
    checkWinCondition() {
        const player = this.gameState.player;
        if (!player) return;

        if (player.passiveIncome >= player.totalExpenses) {
            this.showModalNotification(
                "축하합니다! 🎉",
                `쥐덫에서 탈출했습니다!\n\n${player.name}님의 월 수동 소득(${GameUtils.formatCurrency(player.passiveIncome)})이 월 총 지출(${GameUtils.formatCurrency(player.totalExpenses)})을 넘어섰습니다.\n\n이제 꿈을 실현할 시간입니다!`
            );
        }
    },

    // 모달 알림 표시
    showModalNotification(title, message, onConfirm = null, showCancel = false) {
        const modal = document.getElementById('notification-modal');
        const titleEl = document.getElementById('modal-title');
        const messageEl = document.getElementById('modal-message');
        const confirmBtn = document.getElementById('modal-confirm-btn');
        const cancelBtn = document.getElementById('modal-cancel-btn');

        titleEl.textContent = title;
        messageEl.textContent = message;

        // 버튼 표시/숨김
        if (showCancel) {
            cancelBtn.classList.remove('hidden');
        } else {
            cancelBtn.classList.add('hidden');
        }

        // 확인 버튼 이벤트
        const confirmHandler = () => {
            if (onConfirm) onConfirm();
            modal.classList.add('hidden');
            confirmBtn.removeEventListener('click', confirmHandler);
        };

        confirmBtn.addEventListener('click', confirmHandler);

        // 취소 버튼 이벤트
        const cancelHandler = () => {
            modal.classList.add('hidden');
            cancelBtn.removeEventListener('click', cancelHandler);
        };

        cancelBtn.addEventListener('click', cancelHandler);

        modal.classList.remove('hidden');
    },

    // 카드 검색 필터링
    filterCardsBySearch(cards, searchQuery) {
        if (!searchQuery || searchQuery.trim() === '') {
            return cards;
        }
        
        const query = searchQuery.toLowerCase().trim();
        return cards.filter(card => {
            const title = (card.title || '').toLowerCase();
            const originalTitle = (card.originalTitle || '').toLowerCase();
            const description = (card.description || '').toLowerCase();
            return title.includes(query) || originalTitle.includes(query) || description.includes(query);
        });
    },

    // 카드 목록 렌더링 (기본 버전 - 사용되지 않음)
    renderCardListSimple(cardType) {
        const cardListContainer = document.getElementById('card-list-container');
        if (!cardListContainer) return;

        cardListContainer.innerHTML = '';
        
        // 카드 타입 정규화
        let normalizedCardType = cardType;
        if (cardType === 'SmallDeal' || cardType === 'smalldeal' || cardType === 'smalldeals') {
            normalizedCardType = 'SmallDeals';
        } else if (cardType === 'BigDeal' || cardType === 'bigdeal' || cardType === 'bigdeals') {
            normalizedCardType = 'BigDeals';
        } else if (cardType === 'Doodad' || cardType === 'doodad' || cardType === 'doodads') {
            normalizedCardType = 'Doodads';
        }
        
        let cards = CARD_DATA[normalizedCardType];
        if (!cards || cards.length === 0) {
            cardListContainer.innerHTML = '<div class="text-center p-4 text-gray-500">카드가 없습니다.</div>';
            return;
        }

        // 검색 필터링
        const searchInput = document.getElementById('card-search-input');
        const searchQuery = searchInput ? searchInput.value : '';
        cards = this.filterCardsBySearch(cards, searchQuery);

        if (cards.length === 0) {
            cardListContainer.innerHTML = '<div class="text-center p-4 text-gray-500">검색 결과가 없습니다.</div>';
            return;
        }

        // 카드 정렬: Doodads는 가격순, 나머지는 타이틀순
        if (normalizedCardType === 'Doodads') {
            // 소비 카드는 가격 오름차순으로 정렬
            cards.sort((a, b) => {
                const costA = a.cost || 0;
                const costB = b.cost || 0;
                return costA - costB;
            });
        } else {
            // 다른 카드들은 타이틀 순으로 정렬
            cards.sort((a, b) => a.title.localeCompare(b.title, 'ko'));
        }

        // 간단한 카드 목록 표시
        cards.forEach((card, index) => {
            const cardElement = document.createElement('div');
            cardElement.className = 'bg-white p-4 rounded-lg shadow-sm border hover:shadow-md transition-shadow cursor-pointer mb-3';
            
            cardElement.innerHTML = `
                <h4 class="text-lg font-semibold mb-2">${card.title}</h4>
                <p class="text-sm text-gray-700 mb-2">${card.description}</p>
                <div class="text-sm font-medium text-gray-800">
                    비용: ${GameUtils.formatCurrency(card.cost)}
                </div>
            `;
            
            cardElement.addEventListener('click', () => {
                this.showCardModal(card);
            });
            
            cardListContainer.appendChild(cardElement);
        });
    },

    // 플레이어의 모든 자산을 통합하여 반환 (주식, 채권, 부동산 등)
    getAllPlayerAssets(player) {
        console.log('=== getAllPlayerAssets 호출 ===');
        console.log('받은 player 객체:', player);
        console.log('player.stocks:', player.stocks);
        
        const allAssets = [];
        
        // 1. 일반 자산 (assets 배열) - 주식은 제외
        if (player.assets && player.assets.length > 0) {
            console.log('일반 자산 처리:', player.assets.length, '개');
            player.assets.forEach((asset, index) => {
                // 주식은 제외 (주식은 stocks 객체에서 처리)
                if (asset.type !== 'Stock' && !asset.name.includes('주식')) {
                    // 일관된 ID 생성 (이름과 타입 기반)
                    const consistentId = asset.id || `asset_${asset.name.replace(/[^a-zA-Z0-9가-힣]/g, '_')}_${asset.type || 'Investment'}_${index}`;
                    
                    allAssets.push({
                        id: consistentId,
                        name: asset.name,
                        type: asset.type || 'Investment',
                        shares: asset.shares,
                        totalValue: asset.totalValue || asset.currentValue || asset.downPayment || 0,
                        monthlyIncome: asset.monthlyIncome || 0,
                        averagePrice: asset.averagePrice,
                        totalInvested: asset.totalInvested || asset.purchasePrice || asset.totalValue,
                        originalIndex: index // 원본 배열에서의 인덱스 저장
                    });
                } else {
                    console.log('일반 자산에서 주식 제외:', asset.name);
                }
            });
        }
        
        // 2. 주식 (stocks 객체) - 디버깅 강화
        if (player.stocks && typeof player.stocks === 'object' && Object.keys(player.stocks).length > 0) {
            console.log('주식 처리 시작, stocks 키:', Object.keys(player.stocks));
            
            Object.entries(player.stocks).forEach(([symbol, stockData]) => {
                console.log(`주식 ${symbol} 확인:`, {
                    stockData: stockData,
                    hasShares: !!(stockData && stockData.shares),
                    sharesValue: stockData ? stockData.shares : 'undefined',
                    sharesType: typeof (stockData ? stockData.shares : 'undefined'),
                    passes검증: stockData && 
                              typeof stockData === 'object' && 
                              typeof stockData.shares === 'number' && 
                              stockData.shares > 0
                });
                
                // stockData가 유효하고 shares가 0보다 큰지 확인
                if (stockData && 
                    typeof stockData === 'object' && 
                    typeof stockData.shares === 'number' && 
                    stockData.shares > 0) {
                    
                    // 현재 주식 가치 계산 (평균 매입가로 추정)
                    const shares = stockData.shares;
                    const averagePrice = stockData.averagePrice || 0;
                    const currentValue = shares * averagePrice;
                    
                    const stockAsset = {
                        id: `stock_${symbol}`,
                        name: `${symbol} 주식`,
                        type: 'Stock',
                        shares: shares,
                        totalValue: currentValue,
                        monthlyIncome: stockData.monthlyDividend || 0,
                        averagePrice: averagePrice,
                        totalInvested: stockData.totalInvested || 0
                    };
                    
                    console.log(`주식 자산 추가:`, stockAsset);
                    allAssets.push(stockAsset);
                } else {
                    console.log(`주식 ${symbol} 제외됨`);
                }
            });
        } else {
            console.log('stocks 객체 없음 또는 비어있음:', {
                hasStocks: !!player.stocks,
                stocksType: typeof player.stocks,
                keysLength: player.stocks ? Object.keys(player.stocks).length : 0
            });
        }
        
        // 펀드 처리
        if (player.funds && Object.keys(player.funds).length > 0) {
            console.log('펀드 처리 시작, funds 키:', Object.keys(player.funds));
            Object.entries(player.funds).forEach(([symbol, fundData]) => {
                console.log(`펀드 ${symbol} 확인:`, {
                    fundData: fundData,
                    hasShares: 'shares' in fundData,
                    sharesValue: fundData.shares,
                    sharesType: typeof fundData.shares,
                    totalInvested: fundData.totalInvested,
                    averagePrice: fundData.averagePrice,
                    monthlyDividend: fundData.monthlyDividend,
                    passes검증: fundData.shares && typeof fundData.shares === 'number' && fundData.shares > 0
                });
                
                if (fundData && fundData.shares && typeof fundData.shares === 'number' && fundData.shares > 0) {
                    const fundAsset = {
                        id: `fund_${symbol}`,
                        name: `${symbol} 펀드`,
                        type: 'Fund',
                        shares: fundData.shares,
                        totalValue: fundData.totalInvested || 0,
                        totalInvested: fundData.totalInvested || 0,  // UI에서 사용하는 필드 추가
                        averagePrice: fundData.averagePrice || 0,
                        monthlyIncome: fundData.monthlyDividend || 0,
                        unit: '좌'
                    };
                    console.log('펀드 자산 추가:', fundAsset);
                    allAssets.push(fundAsset);
                } else {
                    console.log(`펀드 ${symbol} 제외됨`);
                }
            });
        } else {
            console.log('funds 객체 없음 또는 비어있음:', {
                hasFunds: !!player.funds,
                fundsType: typeof player.funds,
                keysLength: player.funds ? Object.keys(player.funds).length : 0
            });
        }
        
        console.log('최종 allAssets:', allAssets);
        console.log('=== getAllPlayerAssets 완료 ===');
        return allAssets;
    },

    // 자산/부채 페이지 업데이트 (기존 스타일 적용)
    updateAssetsPage() {
        console.log('=== updateAssetsPage 시작 ===');
        
        const player = this.gameState.player;
        if (!player) {
            console.warn('플레이어 데이터가 없습니다.');
            return;
        }
        
        // DB에서 최신 데이터가 로드되었는지 확인하고 디버깅 정보 출력
        console.log('현재 플레이어 자산/부채 상태:', {
            assets: player.assets ? player.assets.length : 0,
            liabilities: player.liabilities ? player.liabilities.length : 0,
            liabilitiesData: player.liabilities,
            sessionKey: DatabaseManager?.currentSessionKey
        });
        
        const assetsContainer = document.getElementById('assets-list-container');
        const liabilitiesContainer = document.getElementById('liabilities-list-container');
        
        if (!assetsContainer || !liabilitiesContainer) return;

        // 모든 자산 통합
        const allAssets = this.getAllPlayerAssets(player);

        // 자산 섹션
        assetsContainer.innerHTML = '<h3 class="text-lg font-semibold text-gray-700 mb-2">보유 자산</h3>';
        if (allAssets.length === 0) {
            assetsContainer.innerHTML += '<p class="text-sm text-gray-500">보유 중인 투자 자산이 없습니다.</p>';
        } else {
            const list = document.createElement('ul');
            list.className = 'space-y-3';
            allAssets.forEach(asset => {
                const item = document.createElement('li');
                item.className = 'bg-white p-3 rounded-lg shadow border border-gray-200';
                
                // 자산 유형별 표시 형식 - DB 데이터 우선 사용
                let detailsHTML = '';
                if (asset.type === 'Stock') {
                    const shares = asset.shares || 0;
                    const averagePrice = asset.averagePrice || asset.average_price || 0;
                    const totalInvested = asset.totalInvested || asset.total_invested || asset.totalValue || asset.currentValue || 0;
                    const monthlyIncome = asset.monthlyIncome || asset.monthly_income || asset.monthlyDividend || 0;
                    
                    detailsHTML = `
                        <p class="text-xs text-gray-600">보유 수량: ${shares}주 | 평균 매입가: ${GameUtils.formatCurrency(averagePrice)}</p>
                        <p class="text-xs text-gray-600">총 투자금액: ${GameUtils.formatCurrency(totalInvested)} | 월 배당금: ${GameUtils.formatCurrency(monthlyIncome)}</p>
                    `;
                } else if (asset.type === 'Fund') {
                    const shares = asset.shares || 0;
                    const averagePrice = asset.averagePrice || asset.average_price || 0;
                    const totalInvested = asset.totalInvested || asset.total_invested || asset.totalValue || asset.currentValue || 0;
                    const monthlyIncome = asset.monthlyIncome || asset.monthly_income || asset.monthlyDividend || 0;
                    
                    detailsHTML = `
                        <p class="text-xs text-gray-600">보유 수량: ${shares}좌 | 평균 매입가: ${GameUtils.formatCurrency(averagePrice)}</p>
                        <p class="text-xs text-gray-600">총 투자금액: ${GameUtils.formatCurrency(totalInvested)} | 월 수익: ${GameUtils.formatCurrency(monthlyIncome)}</p>
                    `;
                } else if (asset.type === 'RealEstate' || asset.assetType === 'RealEstate') {
                    // 부동산 자산 특별 표시 - DB 데이터 우선 사용
                    const purchasePrice = asset.purchasePrice || asset.purchase_price || asset.totalValue || asset.total_value || asset.currentValue || 0;
                    const downPayment = asset.downPayment || asset.down_payment || 0;
                    const monthlyIncome = asset.monthlyIncome || asset.monthly_income || 0;
                    
                    detailsHTML = `
                        <p class="text-xs text-gray-600">구매가격: ${GameUtils.formatCurrency(purchasePrice)}</p>
                        <p class="text-xs text-gray-600">월 임대수입: ${GameUtils.formatCurrency(monthlyIncome)}</p>
                        <p class="text-xs text-gray-500 italic">※ 판매 시 (판매가 - 구매가) 차액을 현금으로 수령</p>
                    `;
                } else {
                    // 기타 자산 - DB 데이터 우선 사용
                    const assetValue = asset.totalValue || asset.total_value || asset.currentValue || asset.current_value || asset.purchasePrice || 0;
                    const monthlyIncome = asset.monthlyIncome || asset.monthly_income || 0;
                    
                    detailsHTML = `
                        <p class="text-xs text-gray-600">자산 가치: ${GameUtils.formatCurrency(assetValue)} | 월 현금흐름: ${GameUtils.formatCurrency(monthlyIncome)}</p>
                        ${asset.shares ? `<p class="text-xs text-gray-600">보유 수량: ${asset.shares}${asset.unit || '주'}</p>` : ''}
                    `;
                }
                
                item.innerHTML = `
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <h4 class="font-semibold text-blue-700">${asset.name} <span class="text-xs text-gray-500">(${asset.type || 'Investment'})</span></h4>
                            ${detailsHTML}
                        </div>
                        <button data-asset-id="${asset.id}" data-asset-type="${asset.type}" class="sell-asset-btn bg-red-500 text-white px-3 py-1 rounded-md text-xs hover:bg-red-600 transition-colors">판매</button>
                    </div>
                `;
                list.appendChild(item);
            });
            assetsContainer.appendChild(list);
            
            // 자산 판매 버튼 이벤트 바인딩
            assetsContainer.querySelectorAll('.sell-asset-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const assetId = e.currentTarget.dataset.assetId;
                    const assetType = e.currentTarget.dataset.assetType;
                    this.gameState.currentSellingAssetId = assetId;
                    this.showSellAssetModal(assetId, assetType);
                });
            });
        }

        // 부채 섹션
        liabilitiesContainer.innerHTML = '<h3 class="text-lg font-semibold text-gray-700 mb-2">보유 부채</h3>';
        if (player.liabilities.length === 0 && (!player.emergencyLoans || player.emergencyLoans.length === 0)) {
            liabilitiesContainer.innerHTML += '<p class="text-sm text-gray-500">보유 중인 부채가 없습니다.</p>';
        } else {
            const list = document.createElement('ul');
            list.className = 'space-y-3';
            
            // 일반 부채
            if (player.liabilities && player.liabilities.length > 0) {
                player.liabilities.forEach(lib => {
                    // DB 데이터 우선 사용 - amount, remainingAmount, totalAmount 순으로 fallback
                    const debtAmount = lib.remainingAmount || lib.amount || lib.totalAmount || 0;
                    const monthlyPayment = lib.monthlyPayment || 0;
                    
                    const item = document.createElement('li');
                    item.className = 'bg-white p-3 rounded-lg shadow border border-gray-200';
                    item.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-semibold text-red-700">${lib.name} <span class="text-xs text-gray-500">(${lib.type || 'Debt'})</span></h4>
                                <p class="text-xs text-gray-600">잔액: ${GameUtils.formatCurrency(debtAmount)} | 월 상환액: ${monthlyPayment > 0 ? GameUtils.formatCurrency(monthlyPayment) : '없음'}</p>
                            </div>
                            <button data-debt-id="${lib.id}" class="pay-debt-btn bg-green-500 text-white px-3 py-1 rounded-md text-xs hover:bg-green-600 transition-colors">상환</button>
                        </div>
                    `;
                    list.appendChild(item);
                });
            }
            
            // 긴급 대출
            if (player.emergencyLoans && player.emergencyLoans.length > 0) {
                player.emergencyLoans.forEach(loan => {
                    // DB 데이터 우선 사용 - remainingBalance, remainingAmount, amount 순으로 fallback
                    const loanAmount = loan.remainingBalance || loan.remainingAmount || loan.loanAmount || loan.amount || 0;
                    const monthlyPayment = loan.monthlyPayment || 0;
                    
                    const item = document.createElement('li');
                    item.className = 'bg-white p-3 rounded-lg shadow border border-gray-200';
                    item.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-semibold text-yellow-700">긴급 대출 <span class="text-xs text-gray-500">(Emergency Loan)</span></h4>
                                <p class="text-xs text-gray-600">잔액: ${GameUtils.formatCurrency(loanAmount)} | 월 이자: ${GameUtils.formatCurrency(monthlyPayment)}</p>
                            </div>
                            <button data-debt-id="${loan.id}" class="pay-debt-btn bg-green-500 text-white px-3 py-1 rounded-md text-xs hover:bg-green-600 transition-colors">상환</button>
                        </div>
                    `;
                    list.appendChild(item);
                });
            }
            
            liabilitiesContainer.appendChild(list);
            
            // 부채 상환 버튼 이벤트 바인딩
            liabilitiesContainer.querySelectorAll('.pay-debt-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const debtId = e.currentTarget.dataset.debtId;
                    const debt = player.liabilities ? player.liabilities.find(d => d.id === debtId) : null;
                    const emergencyLoan = player.emergencyLoans ? player.emergencyLoans.find(d => d.id === debtId) : null;
                    
                    if (emergencyLoan) {
                        this.repayEmergencyLoan(debtId);
                    } else if (debt) {
                        this.showPayDebtModal(debtId);
                    }
                });
            });
        }
    },

    // 재무제표 업데이트 (기존 스타일 적용)
    updateFinancialReport() {
        const player = this.gameState.player;
        if (!player) return;
        
        const container = document.getElementById('financial-report-container');
        if (!container) return;
        
        container.innerHTML = '';

        // 모든 계산 값에 null 체크 추가
        const totalInvestmentValue = player.assets ? FinancialCalculator.calculateTotalAssets(player.assets) : 0;
        const totalPlayerAssets = (player.cash || 0) + totalInvestmentValue;
        const totalPlayerLiabilities = player.liabilities ? FinancialCalculator.calculateTotalLiabilities(player.liabilities) : 0;
        
        // 긴급 대출 부채 추가
        const emergencyLoanDebt = player.emergencyLoans ? player.emergencyLoans.reduce((sum, loan) => sum + (loan.remainingAmount || 0), 0) : 0;
        const totalLiabilities = totalPlayerLiabilities + emergencyLoanDebt;
        
        const netWorth = FinancialCalculator.calculateNetWorth(totalPlayerAssets, totalLiabilities);

        // null 체크를 위한 헬퍼 함수
        const safeFormatCurrency = (amount) => {
            return GameUtils.formatCurrency(amount || 0);
        };

        // Income Statement
        let incomeHTML = `
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                <h3 class="text-md font-semibold text-gray-700 mb-2 border-b pb-1">손익 계산서</h3>
                <dl class="text-sm space-y-1">
                    <div class="flex justify-between"><dt>월급:</dt><dd class="text-green-600">${safeFormatCurrency(player.salary)}</dd></div>
                    <div class="flex justify-between"><dt>자산 소득 (월):</dt><dd class="text-green-600">${safeFormatCurrency(player.passiveIncome)}</dd></div>
                    <div class="flex justify-between font-bold"><dt>총 수입 (월):</dt><dd class="text-green-700">${safeFormatCurrency(player.totalIncome)}</dd></div>
                    <hr class="my-2">
                    <div class="flex justify-between"><dt>기본 지출 (월):</dt><dd class="text-red-600">${safeFormatCurrency(player.expenses ? (player.expenses.taxes + player.expenses.homePayment + player.expenses.schoolLoan + player.expenses.carLoan + player.expenses.creditCard + player.expenses.retail + player.expenses.other) : 0)}</dd></div>
                    <div class="flex justify-between"><dt>자녀 양육비 (월):</dt><dd class="text-red-600">${safeFormatCurrency(player.expenses && player.expenses.childrenCount ? player.expenses.children : 0)}</dd></div>
                    <div class="flex justify-between"><dt>총 부채 상환액 (월):</dt><dd class="text-red-600">${safeFormatCurrency(player.liabilities ? FinancialCalculator.calculateDebtExpenses(player.liabilities) : 0)}</dd></div>
                    <div class="flex justify-between font-bold"><dt>총 지출 (월):</dt><dd class="text-red-700">${safeFormatCurrency(player.totalExpenses)}</dd></div>
                    <hr class="my-2">
                    <div class="flex justify-between font-bold text-lg"><dt>월 현금흐름:</dt><dd class="${(player.monthlyCashFlow || 0) >= 0 ? 'text-blue-600' : 'text-red-600'}">${safeFormatCurrency(player.monthlyCashFlow)}</dd></div>
                </dl>
            </div>
        `;
        container.innerHTML += incomeHTML;

        // Balance Sheet
        let balanceSheetHTML = `
            <div class="bg-white p-4 rounded-lg shadow border border-gray-200 mt-4">
                <h3 class="text-md font-semibold text-gray-700 mb-2 border-b pb-1">재무 상태표</h3>
                <div class="text-sm space-y-1">
                    <h4 class="font-medium mt-2 mb-1 text-green-700">자산</h4>
                    <div class="flex justify-between"><dt>현금:</dt><dd>${safeFormatCurrency(player.cash)}</dd></div>`;
        
        if (player.assets && player.assets.length > 0) {
            player.assets.forEach(asset => {
                balanceSheetHTML += `<div class="flex justify-between ml-2"><dt>${asset.name}:</dt><dd>${safeFormatCurrency(asset.currentValue || asset.totalValue || asset.downPayment)}</dd></div>`;
            });
        }
        
        if (player.stocks && Object.keys(player.stocks).length > 0) {
            Object.entries(player.stocks).forEach(([symbol, data]) => {
                const stockValue = data.shares * data.averagePrice;
                balanceSheetHTML += `<div class="flex justify-between ml-2"><dt>${symbol} 주식:</dt><dd>${safeFormatCurrency(stockValue)}</dd></div>`;
            });
        }
        
        balanceSheetHTML += `<div class="flex justify-between font-bold"><dt>총 자산:</dt><dd>${safeFormatCurrency(totalPlayerAssets)}</dd></div>`;
        
        balanceSheetHTML += `<h4 class="font-medium mt-3 mb-1 text-red-700">부채</h4>`;
        
        if (player.liabilities && player.liabilities.length > 0) {
            player.liabilities.forEach(lib => {
                balanceSheetHTML += `<div class="flex justify-between ml-2"><dt>${lib.name}:</dt><dd>${safeFormatCurrency(lib.remainingAmount || 0)}</dd></div>`;
            });
        }
        
        if (player.emergencyLoans && player.emergencyLoans.length > 0) {
            player.emergencyLoans.forEach(loan => {
                balanceSheetHTML += `<div class="flex justify-between ml-2"><dt>긴급 대출:</dt><dd>${safeFormatCurrency(loan.remainingAmount)}</dd></div>`;
            });
        }
        
        balanceSheetHTML += `<div class="flex justify-between font-bold"><dt>총 부채:</dt><dd>${safeFormatCurrency(totalLiabilities)}</dd></div>`;
        
        balanceSheetHTML += `
                    <hr class="my-2">
                    <div class="flex justify-between font-bold text-lg"><dt>순자산:</dt><dd class="${netWorth >= 0 ? 'text-blue-600' : 'text-red-600'}">${safeFormatCurrency(netWorth)}</dd></div>
                </div>
            </div>
        `;
        container.innerHTML += balanceSheetHTML;
    },

    // 꿈 정보 섹션 업데이트 (기존 스타일 적용)
    updateDreamInfo() {
        const player = this.gameState.player;
        if (!player) return;
        
        // 자산/부채 탭 상단에 꿈 정보 표시 (기존 방식과 동일)
        const assetsTab = document.getElementById('tab-content-assets');
        let dreamInfoSection = document.getElementById('dream-info-section');
        
        if (!dreamInfoSection) {
            // 없으면 새로 생성하여 탭 컨텐츠의 맨 위에 추가
            dreamInfoSection = document.createElement('div');
            dreamInfoSection.id = 'dream-info-section';
            dreamInfoSection.className = 'bg-gradient-to-r from-blue-50 to-indigo-50 p-3 rounded-lg shadow mb-4';
            
            const firstChild = assetsTab.firstChild;
            assetsTab.insertBefore(dreamInfoSection, firstChild);
        }
        
        // 꿈 정보 업데이트 (단순 텍스트만 표시 - 기존 스타일)
        dreamInfoSection.innerHTML = `
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold text-gray-700">나의 꿈: ${player.dream}</h3>
                <div class="text-xs text-blue-600 font-bold">목표 금액: ${GameUtils.formatCurrency(player.dreamCost)}</div>
            </div>
        `;
    },

    // 자녀 버튼 상태 업데이트 (script.js.backup 기반)
    updateChildButtonState() {
        const player = this.gameState.player;
        if (!player) return;

        const childBtn = document.getElementById('have-child-btn');
        if (!childBtn) return;

        const childrenCount = player.expenses.childrenCount || 0;
        const maxChildren = 3;
        const monthlyChildExpense = player.expenses.perChildExpense || 200;

        if (childrenCount >= maxChildren) {
            // 최대 자녀 수에 도달한 경우
            childBtn.innerHTML = `출산하기<br/>(자녀 ${childrenCount}/${maxChildren})`;
            childBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            childBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            childBtn.disabled = true;
        } else {
            // 아직 자녀를 더 가질 수 있는 경우
            childBtn.innerHTML = `출산하기<br/>(자녀 ${childrenCount}/${maxChildren})`;
            childBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            childBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            childBtn.disabled = false;
        }
    },

    // 카드 목록 렌더링 함수
    renderCardList(cardType) {
        console.log(`카드 렌더링: ${cardType}`);
        const cardListContainer = document.getElementById('card-list-container');
        cardListContainer.innerHTML = '';
        
        // 카드 타입 정규화
        let normalizedCardType = cardType;
        if (cardType === 'SmallDeal' || cardType === 'smalldeal' || cardType === 'smalldeals') {
            normalizedCardType = 'SmallDeals';
        } else if (cardType === 'BigDeal' || cardType === 'bigdeal' || cardType === 'bigdeals') {
            normalizedCardType = 'BigDeals';
        } else if (cardType === 'Doodad' || cardType === 'doodad' || cardType === 'doodads') {
            normalizedCardType = 'Doodads';
        }
        
        let cards = CARD_DATA[normalizedCardType];
        if (!cards || cards.length === 0) {
            cardListContainer.innerHTML = '<div class="text-center p-4 text-gray-500">카드가 없습니다.</div>';
            return;
        }
        
        // SmallDeals, BigDeals 카드는 카테고리 탭과 함께 표시
        if (normalizedCardType === 'SmallDeals' || normalizedCardType === 'BigDeals') {
            this.renderCategoryTabs(normalizedCardType);
            this.renderCardsByCategory(cards, normalizedCardType, '전체');
            return;
        } else {
            // Doodads의 경우 카테고리 탭 숨기기
            this.hideCategoryTabs();
        }
        
        // Doodads 카드인 경우 자녀 관련 카드 필터링
        if (normalizedCardType === 'Doodads') {
            const player = this.gameState.player;
            
            // 자녀가 없는 경우 자녀 관련 카드는 비활성화 상태로 표시
            if (player && player.expenses.childrenCount === 0) {
                cards = cards.map(card => {
                    if (this.CHILD_RELATED_CARDS.includes(card.title)) {
                        return {
                            ...card,
                            disabled: true,
                            disabledReason: "자녀가 없어 사용할 수 없습니다."
                        };
                    }
                    return card;
                });
            }
        }
        
        // 검색 필터링
        const searchInput = document.getElementById('card-search-input');
        const searchQuery = searchInput ? searchInput.value : '';
        cards = this.filterCardsBySearch(cards, searchQuery);

        if (cards.length === 0) {
            cardListContainer.innerHTML = '<div class="text-center p-4 text-gray-500">검색 결과가 없습니다.</div>';
            return;
        }
        
        // 카드 정렬: Doodads는 가격순, 나머지는 타이틀순
        if (normalizedCardType === 'Doodads') {
            // 소비 카드는 가격 오름차순으로 정렬
            cards.sort((a, b) => {
                const costA = a.cost || 0;
                const costB = b.cost || 0;
                return costA - costB;
            });
        } else {
            // 다른 카드들은 타이틀 순으로 정렬
            cards.sort((a, b) => a.title.localeCompare(b.title, 'ko'));
        }
        
        // 카드를 반복하여 DOM 요소 생성
        cards.forEach((card, index) => {
            const cardElement = this.createCardListItem(card, normalizedCardType, index);
            cardListContainer.appendChild(cardElement);
        });
    },

    // 개별 카드 리스트 아이템 생성
    createCardListItem(card, cardType, index) {
        const cardElement = document.createElement('div');
        const isDisabled = card.disabled || false;
        
        cardElement.className = `card-item bg-white p-4 rounded-lg shadow-sm border hover:shadow-md transition-all cursor-pointer ${
            isDisabled ? 'opacity-50 cursor-not-allowed' : ''
        }`;
        
        // 카드 타입별 배경색 설정
        let bgColor = 'border-gray-200';
        if (cardType === 'SmallDeals') {
            bgColor = 'border-l-4 border-l-green-500';
        } else if (cardType === 'BigDeals') {
            bgColor = 'border-l-4 border-l-blue-500';
        } else if (cardType === 'Doodads' || cardType === 'Doodad') {
            bgColor = 'border-l-4 border-l-red-500';
        }
        
        cardElement.classList.add(...bgColor.split(' '));
        
        // 카드 내용 구성
        let cardContent = `
            <div class="flex justify-between items-start mb-2">
                <h4 class="font-semibold text-gray-800 text-sm">${card.title}</h4>
                ${card.cost ? `<span class="text-sm font-bold text-green-600">${GameUtils.formatCurrency(card.cost)}</span>` : ''}
            </div>
            <p class="text-xs text-gray-600 mb-2">${card.description || ''}</p>
        `;
        
        // 자산 세부 정보 추가
        if (card.assetDetails) {
            const details = card.assetDetails;
            cardContent += `
                <div class="text-xs text-gray-500 space-y-1">
                    ${details.assetType ? `<div>유형: ${details.assetType}</div>` : ''}
                    ${details.monthlyIncome ? `<div>월 수입: ${GameUtils.formatCurrency(details.monthlyIncome)}</div>` : ''}
                    ${details.downPayment ? `<div>계약금: ${GameUtils.formatCurrency(details.downPayment)}</div>` : ''}
                </div>
            `;
        }
        
        // 비활성화된 카드의 경우 사유 표시
        if (isDisabled && card.disabledReason) {
            cardContent += `
                <div class="mt-2 p-2 bg-red-50 rounded text-xs text-red-600">
                    ${card.disabledReason}
                </div>
            `;
        }
        
        cardElement.innerHTML = cardContent;
        
        // 클릭 이벤트 추가 (비활성화되지 않은 경우만)
        if (!isDisabled) {
            cardElement.addEventListener('click', () => {
                this.showCardModal(card);
            });
        }
        
        return cardElement;
    },

    // 탭 전환 함수
    switchTab(tabName) {
        // 모든 탭 콘텐츠를 숨김 (CSS 클래스와 display 스타일 모두 적용)
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
            tab.style.display = 'none';
        });
        
        // 선택한 탭 콘텐츠만 표시 (CSS 클래스와 display 스타일 모두 적용)
        const selectedTab = document.getElementById(`tab-content-${tabName}`);
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
            selectedTab.style.display = 'block';
        }
        
        // 모든 탭 버튼에서 active 클래스 제거
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('text-blue-600');
            btn.classList.remove('bg-blue-50');
        });
        
        // 선택한 탭 버튼에만 active 클래스 추가
        const activeBtn = document.querySelector(`.nav-btn[data-tab="${tabName}"]`);
        if (activeBtn) {
            activeBtn.classList.add('text-blue-600');
            activeBtn.classList.add('bg-blue-50');
        }
    },

    // 모달 알림 표시
    showModalNotification(titleText, messageText, callback = null, showCancelButton = false) {
        const modal = document.getElementById('card-modal');
        if (!modal) {
            console.error('card-modal 요소를 찾을 수 없습니다.');
            return;
        }
        
        const titleElement = document.getElementById('card-title');
        const descriptionElement = document.getElementById('card-description');
        const detailsInputsElement = document.getElementById('card-details-inputs');
        const actionsElement = document.getElementById('card-actions');
        
        if (!titleElement || !descriptionElement || !detailsInputsElement || !actionsElement) {
            console.error('모달 내부 요소들을 찾을 수 없습니다.');
            return;
        }
        
        titleElement.textContent = titleText;
        descriptionElement.innerHTML = messageText.replace(/\n/g, '<br>');
        detailsInputsElement.innerHTML = '';
        actionsElement.innerHTML = '';
        
        if (showCancelButton) {
            // 취소 버튼과 확인 버튼을 나란히 배치
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'flex space-x-3';
            
            // 취소 버튼
            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'flex-1 bg-gray-300 text-gray-800 py-2.5 rounded-lg font-semibold hover:bg-gray-400 text-sm';
            cancelBtn.textContent = '취소';
            cancelBtn.onclick = () => this.hideCardModal();
            
            // 확인 버튼
            const okBtn = document.createElement('button');
            okBtn.className = 'flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 text-sm';
            okBtn.textContent = '확인';
            okBtn.onclick = () => {
                this.hideCardModal();
                if (callback) callback();
            };
            
            buttonContainer.appendChild(cancelBtn);
            buttonContainer.appendChild(okBtn);
            actionsElement.appendChild(buttonContainer);
        } else {
            // 기존처럼 확인 버튼만 표시
            const okBtn = document.createElement('button');
            okBtn.className = 'w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 text-sm';
            okBtn.textContent = '확인';
            okBtn.onclick = () => {
                this.hideCardModal();
                if (callback) callback();
            };
            actionsElement.appendChild(okBtn);
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        const modalContent = modal.children[0];
        if (modalContent) {
            modalContent.classList.remove('opacity-0', 'scale-95');
            modalContent.classList.add('opacity-100', 'scale-100');
        }
    },

    // 카드 모달 숨기기
    hideCardModal() {
        const modal = document.getElementById('card-modal');
        if (!modal) {
            console.error('card-modal 요소를 찾을 수 없습니다.');
            return;
        }
        
        const modalContent = modal.children[0];
        if (modalContent) {
            modalContent.classList.remove('opacity-100', 'scale-100');
            modalContent.classList.add('opacity-0', 'scale-95');
        }
        
        // 애니메이션 후 숨기기
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 150);
    },

    // 카드 모달 표시 (단일 카드용)
    showCardModal(card) {
        console.log('=== showCardModal 호출 ===');
        console.log('카드 정보:', card);
        console.log('주식 여부:', card.assetDetails && card.assetDetails.assetType === 'Stock');
        console.log('카드 타입:', card.cardType);
        console.log('asset 세부정보:', card.assetDetails);
        
        const modal = document.getElementById('card-modal');
        if (!modal) {
            console.error('card-modal 요소를 찾을 수 없습니다.');
            return;
        }
        
        const titleElement = document.getElementById('card-title');
        const descriptionElement = document.getElementById('card-description');
        const detailsInputsElement = document.getElementById('card-details-inputs');
        const actionsElement = document.getElementById('card-actions');
        
        if (!titleElement || !descriptionElement || !detailsInputsElement || !actionsElement) {
            console.error('모달 내부 요소들을 찾을 수 없습니다.');
            return;
        }
        
        // 카드 제목과 설명 설정
        titleElement.textContent = card.title;
        descriptionElement.innerHTML = card.description || '';
        
        // 카드 세부 정보 표시
        let detailsHTML = '';
        const isStock = card.assetDetails && card.assetDetails.assetType === 'Stock';
        
        // StockEvent 감지 로직
        const isStockEvent = card.assetDetails && card.assetDetails.assetType === 'StockEvent';
        
        // 추가 주식 감지 로직 (제목 기반)
        const isTitleStock = card.title && card.title.includes('주식');
        const finalIsStock = isStock || isTitleStock;
        
        // 펀드 감지 로직
        const isFund = (card.assetDetails && card.assetDetails.assetType === 'Investment') || 
                       (card.title && card.title.includes('펀드'));
        
        // 수량 선택이 가능한 투자 상품 (주식 또는 펀드, StockEvent 제외)
        const isQuantitySelectable = (finalIsStock || isFund) && !isStockEvent;
        
        console.log('투자 상품 감지 결과:', {
            assetDetailsStock: isStock,
            titleStock: isTitleStock,
            finalIsStock: finalIsStock,
            isFund: isFund,
            isQuantitySelectable: isQuantitySelectable,
            cardTitle: card.title
        });
        
        if (card.cost && !isStockEvent) {
            if (finalIsStock) {
                detailsHTML += `<div class="text-sm"><strong>주당 가격:</strong> ${GameUtils.formatCurrency(card.cost)}</div>`;
            } else if (isFund) {
                detailsHTML += `<div class="text-sm"><strong>좌당 가격:</strong> ${GameUtils.formatCurrency(card.cost)}</div>`;
            } else {
                detailsHTML += `<div class="text-sm"><strong>비용:</strong> ${GameUtils.formatCurrency(card.cost)}</div>`;
            }
        }
        
        if (card.assetDetails) {
            const details = card.assetDetails;
            
            // StockEvent 카드의 경우 특별한 정보 표시
            if (isStockEvent) {
                const stockSymbol = this.extractStockSymbol(details.assetName);
                const player = this.gameState.player;
                const hasStock = player && player.stocks && player.stocks[stockSymbol];
                
                detailsHTML += `<div class="text-sm"><strong>대상 주식:</strong> ${stockSymbol}</div>`;
                detailsHTML += `<div class="text-sm mb-3"><strong>현재 보유 여부:</strong> ${hasStock ? '보유 중' : '미보유'}</div>`;
                
                if (hasStock) {
                    const stockInfo = player.stocks[stockSymbol];
                    detailsHTML += `
                        <div class="p-3 bg-green-50 rounded-lg border border-green-200 mb-4">
                            <div class="text-sm font-medium text-green-800 mb-1">현재 보유 정보</div>
                            <div class="text-sm text-green-700">보유 수량: ${stockInfo.shares}주</div>
                            <div class="text-sm text-green-700">평균 매입가: ${GameUtils.formatCurrency(stockInfo.averagePrice)}</div>
                        </div>`;
                    
                    // 분할/병합 옵션 선택
                    detailsHTML += `
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">이벤트 유형을 선택하세요:</label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                                    <input type="radio" name="stock-event-type" value="split" class="mr-3 text-blue-600" checked>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">📈 주식 분할 (2 for 1)</div>
                                        <div class="text-sm text-gray-600">보유 수량이 2배로 증가, 매입가는 절반으로 감소</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-red-50 transition-colors">
                                    <input type="radio" name="stock-event-type" value="reverse_split" class="mr-3 text-red-600">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">🔄 주식 병합 (1 for 2)</div>
                                        <div class="text-sm text-gray-600">보유 수량이 절반으로 감소, 매입가는 2배로 증가</div>
                                    </div>
                                </label>
                            </div>
                        </div>`;
                } else {
                    detailsHTML += `
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 mb-3">
                            <div class="text-sm text-gray-600">${stockSymbol} 주식을 보유하고 있지 않아 이 이벤트의 영향을 받지 않습니다.</div>
                        </div>`;
                }
            } else {
                if (details.assetType) {
                    detailsHTML += `<div class="text-sm"><strong>유형:</strong> ${details.assetType}</div>`;
                }
                
                // tradingRange 정보 추가
                if (details.tradingRange) {
                    const currentPrice = card.cost;
                    const tradingRangeInfo = this.parseTradingRange(details.tradingRange, currentPrice, finalIsStock, isFund);
                    detailsHTML += tradingRangeInfo;
                }
            }
            
            if (isQuantitySelectable) {
                // 주식 또는 펀드인 경우 배당금/수익 정보
                const dividendPerShare = details?.dividendsPerShare || card.cashFlowChange || 0;
                if (dividendPerShare > 0) {
                    if (finalIsStock) {
                        detailsHTML += `<div class="text-sm"><strong>주당 월배당:</strong> ${GameUtils.formatCurrency(dividendPerShare)}</div>`;
                    } else if (isFund) {
                        detailsHTML += `<div class="text-sm"><strong>좌당 월수익:</strong> ${GameUtils.formatCurrency(dividendPerShare)}</div>`;
                    }
                }
                
                // 수량 입력 필드
                const unit = finalIsStock ? '주' : '좌';
                const unitText = finalIsStock ? '1주' : '1좌';
                const unitText5 = finalIsStock ? '5주' : '5좌';
                const unitText10 = finalIsStock ? '10주' : '10좌';
                
                console.log(`✅ ${finalIsStock ? '주식' : '펀드'}으로 인식됨 - UI 추가 중...`);
                console.log('배당금/수익 정보:', dividendPerShare);
                detailsHTML += `
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border">
                        <label for="stock-shares" class="block text-sm font-medium text-gray-700 mb-2">구매 수량</label>
                        <div class="flex items-center space-x-3 mb-2">
                            <input type="number" id="stock-shares" min="1" value="1" 
                                   class="flex-1 min-w-0 w-20 max-w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-center">
                            <span class="text-sm text-gray-600 whitespace-nowrap">${unit}</span>
                        </div>
                        <div class="flex space-x-2 mb-2">
                            <button type="button" class="quick-shares-btn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs" data-shares="1">${unitText}</button>
                            <button type="button" class="quick-shares-btn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs" data-shares="5">${unitText5}</button>
                            <button type="button" class="quick-shares-btn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs" data-shares="10">${unitText10}</button>
                            <button type="button" class="quick-shares-btn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs" data-shares="최대">최대</button>
                        </div>
                        <div class="mt-2">
                            <div class="text-xs text-gray-600">총 비용: <span id="total-cost" class="font-semibold">${GameUtils.formatCurrency(card.cost)}</span></div>
                            <div class="text-xs text-gray-600">${finalIsStock ? '월 배당금' : '월 수익'}: <span id="total-dividend" class="font-semibold">${GameUtils.formatCurrency(dividendPerShare)}</span></div>
                            <div class="text-xs text-gray-500 mt-1">보유 현금: ${GameUtils.formatCurrency(this.gameState.player?.cash || 0)}</div>
                        </div>
                    </div>
                `;
            } else {
                // 일반 자산인 경우
                if (details?.monthlyIncome) {
                    detailsHTML += `<div class="text-sm"><strong>월 수입:</strong> ${GameUtils.formatCurrency(details.monthlyIncome)}</div>`;
                }
                if (details?.downPayment) {
                    detailsHTML += `<div class="text-sm"><strong>계약금:</strong> ${GameUtils.formatCurrency(details.downPayment)}</div>`;
                }
            }
        } else if (isQuantitySelectable) {
            // assetDetails가 없지만 제목으로 주식/펀드 인식된 경우
            const unit = finalIsStock ? '주' : '좌';
            const unitText = finalIsStock ? '1주' : '1좌';
            const unitText5 = finalIsStock ? '5주' : '5좌';
            const unitText10 = finalIsStock ? '10주' : '10좌';
            
            console.log(`✅ 제목 기반 ${finalIsStock ? '주식' : '펀드'} 인식 - UI 추가 중...`);
            const dividendPerShare = card.cashFlowChange || 0;
            if (dividendPerShare > 0) {
                if (finalIsStock) {
                    detailsHTML += `<div class="text-sm"><strong>주당 월배당:</strong> ${GameUtils.formatCurrency(dividendPerShare)}</div>`;
                } else if (isFund) {
                    detailsHTML += `<div class="text-sm"><strong>좌당 월수익:</strong> ${GameUtils.formatCurrency(dividendPerShare)}</div>`;
                }
            }
            
            // 수량 입력 필드
            detailsHTML += `
                <div class="mt-4 p-3 bg-blue-50 rounded-lg border">
                    <label for="stock-shares" class="block text-sm font-medium text-gray-700 mb-2">구매 수량</label>
                    <div class="flex items-center space-x-3 mb-2">
                        <input type="number" id="stock-shares" min="1" value="1" 
                               class="flex-1 min-w-0 w-20 max-w-24 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-center">
                        <span class="text-sm text-gray-600 whitespace-nowrap">${unit}</span>
                    </div>
                    <div class="flex space-x-2 mb-2">
                        <button type="button" class="quick-shares-btn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs" data-shares="1">${unitText}</button>
                        <button type="button" class="quick-shares-btn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs" data-shares="5">${unitText5}</button>
                        <button type="button" class="quick-shares-btn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs" data-shares="10">${unitText10}</button>
                        <button type="button" class="quick-shares-btn flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded text-xs" data-shares="최대">최대</button>
                    </div>
                    <div class="mt-2">
                        <div class="text-xs text-gray-600">총 비용: <span id="total-cost" class="font-semibold">${GameUtils.formatCurrency(card.cost)}</span></div>
                        <div class="text-xs text-gray-600">${finalIsStock ? '월 배당금' : '월 수익'}: <span id="total-dividend" class="font-semibold">${GameUtils.formatCurrency(dividendPerShare)}</span></div>
                        <div class="text-xs text-gray-500 mt-1">보유 현금: ${GameUtils.formatCurrency(this.gameState.player?.cash || 0)}</div>
                    </div>
                </div>
            `;
        }
        
        // 지불 방법 선택 UI 추가 (paymentOptions가 있는 카드인 경우)
        if (card.paymentOptions && Array.isArray(card.paymentOptions)) {
            detailsHTML += `
                <div class="mt-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                    <label for="payment-method" class="block text-sm font-medium text-gray-700 mb-2">지불 방법 선택</label>
                    <select id="payment-method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 mb-3">
                        ${card.paymentOptions.map((option, index) => 
                            `<option value="${index}">${option.label} - ${GameUtils.formatCurrency(option.downPayment || option.cost)}</option>`
                        ).join('')}
                    </select>
                    <div id="payment-details" class="text-sm space-y-1 bg-white p-3 rounded border">
                        <!-- 선택된 지불 방법의 세부 내용이 여기에 표시됩니다 -->
                    </div>
                </div>
            `;
        }
        
        detailsInputsElement.innerHTML = detailsHTML;
        console.log('모달 HTML 생성 완료. 투자상품 여부:', isQuantitySelectable);
        console.log('생성된 HTML:', detailsHTML);
        
        // 수량 선택 가능한 투자상품인 경우 수량 변경 시 총 비용 업데이트
        if (isQuantitySelectable) {
            console.log(`✅ ${finalIsStock ? '주식' : '펀드'} 이벤트 바인딩 시작`);
            const sharesInput = document.getElementById('stock-shares');
            const totalCostSpan = document.getElementById('total-cost');
            const totalDividendSpan = document.getElementById('total-dividend');
            const dividendPerShare = card.assetDetails?.dividendsPerShare || card.cashFlowChange || 0;
            
            console.log('요소 찾기 결과:', {
                sharesInput: !!sharesInput,
                totalCostSpan: !!totalCostSpan,
                totalDividendSpan: !!totalDividendSpan,
                dividendPerShare
            });
            
            const updateTotals = () => {
                const shares = parseInt(sharesInput.value) || 1;
                const totalCost = card.cost * shares;
                const totalDividend = dividendPerShare * shares;
                totalCostSpan.textContent = GameUtils.formatCurrency(totalCost);
                totalDividendSpan.textContent = GameUtils.formatCurrency(totalDividend);
                
                // 현금 부족 체크 (플레이어 현금과 비교)
                const player = this.gameState.player;
                if (player && totalCost > player.cash) {
                    totalCostSpan.style.color = '#ef4444'; // 빨간색
                    sharesInput.style.borderColor = '#ef4444';
                } else {
                    totalCostSpan.style.color = '#374151'; // 기본색
                    sharesInput.style.borderColor = '#d1d5db';
                }
            };
            
            sharesInput.addEventListener('input', updateTotals);
            sharesInput.addEventListener('change', updateTotals);
            
            // 빠른 선택 버튼 이벤트
            document.querySelectorAll('.quick-shares-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const shares = e.target.dataset.shares;
                    if (shares === '최대') {
                        // 최대 구매 가능한 주식 수 계산
                        const maxShares = Math.floor((this.gameState.player?.cash || 0) / card.cost);
                        sharesInput.value = Math.max(1, maxShares);
                    } else {
                        sharesInput.value = shares;
                    }
                    updateTotals();
                });
            });
        }
        
        // 지불 방법 선택 이벤트 리스너 추가
        if (card.paymentOptions && Array.isArray(card.paymentOptions)) {
            const paymentMethodSelect = document.getElementById('payment-method');
            const paymentDetailsDiv = document.getElementById('payment-details');
            
            if (paymentMethodSelect && paymentDetailsDiv) {
                // 초기 지불 방법 세부 내용 표시
                const updatePaymentDetails = () => {
                    const selectedIndex = parseInt(paymentMethodSelect.value);
                    const selectedOption = card.paymentOptions[selectedIndex];
                    
                    if (selectedOption) {
                        let detailsHTML = `<div class="font-medium text-gray-800 mb-2">${selectedOption.label}</div>`;
                        detailsHTML += `<div class="text-gray-700">${selectedOption.description}</div>`;
                        
                        detailsHTML += `<div class="mt-2 pt-2 border-t border-gray-200 space-y-1">`;
                        if (selectedOption.downPayment && selectedOption.downPayment > 0) {
                            detailsHTML += `<div class="flex justify-between"><span>계약금:</span><span class="font-medium">${GameUtils.formatCurrency(selectedOption.downPayment)}</span></div>`;
                        }
                        if (selectedOption.debtIncurred && selectedOption.debtIncurred > 0) {
                            detailsHTML += `<div class="flex justify-between"><span>대출 발생:</span><span class="font-medium text-red-600">${GameUtils.formatCurrency(selectedOption.debtIncurred)}</span></div>`;
                        }
                        if (selectedOption.monthlyExpenseIncrease && selectedOption.monthlyExpenseIncrease > 0) {
                            detailsHTML += `<div class="flex justify-between"><span>월 상환액:</span><span class="font-medium text-orange-600">${GameUtils.formatCurrency(selectedOption.monthlyExpenseIncrease)}</span></div>`;
                        }
                        detailsHTML += `<div class="flex justify-between border-t pt-1 mt-1"><span class="font-medium">총 비용:</span><span class="font-bold">${GameUtils.formatCurrency(selectedOption.cost)}</span></div>`;
                        detailsHTML += `</div>`;
                        
                        // 현금 부족 체크
                        const player = this.gameState.player;
                        const requiredCash = selectedOption.downPayment || selectedOption.cost;
                        if (player && requiredCash > player.cash) {
                            detailsHTML += `<div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-red-700 text-xs">
                                <strong>⚠️ 현금 부족:</strong> ${GameUtils.formatCurrency(requiredCash - player.cash)} 더 필요
                            </div>`;
                        }
                        
                        paymentDetailsDiv.innerHTML = detailsHTML;
                    }
                };
                
                // 초기 표시
                updatePaymentDetails();
                
                // 선택 변경 시 업데이트
                paymentMethodSelect.addEventListener('change', updatePaymentDetails);
            }
        }
        
        // 액션 버튼 생성
        actionsElement.innerHTML = '';
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'flex space-x-3';
        
        // 취소 버튼
        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'flex-1 bg-gray-300 text-gray-800 py-2.5 rounded-lg font-semibold hover:bg-gray-400 text-sm';
        cancelBtn.textContent = '취소';
        cancelBtn.onclick = () => this.hideCardModal();
        
        // 구매/사용 버튼
        const actionBtn = document.createElement('button');
        actionBtn.className = 'flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 text-sm';
        
        // 카드 타입에 따라 버튼 텍스트 설정
        if (isStockEvent) {
            const player = this.gameState.player;
            const stockSymbol = this.extractStockSymbol(card.assetDetails.assetName);
            const hasStock = player && player.stocks && player.stocks[stockSymbol];
            
            if (hasStock) {
                actionBtn.textContent = '이벤트 적용';
                actionBtn.className = 'flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 text-sm';
            } else {
                actionBtn.textContent = '확인';
                actionBtn.className = 'flex-1 bg-gray-600 text-white py-2.5 rounded-lg font-semibold hover:bg-gray-700 text-sm';
            }
        } else if (card.cardType === 'Doodad' || card.cardType === 'Doodads') {
            actionBtn.textContent = '구매하기';
        } else if (finalIsStock) {
            actionBtn.textContent = '주식 구매';
        } else if (isFund) {
            actionBtn.textContent = '펀드 구매';
        } else {
            actionBtn.textContent = '투자하기';
        }
        
        actionBtn.onclick = () => {
            console.log('✅ 액션 버튼 클릭됨');
            console.log('✅ 버튼 클릭 시 투자상품 여부:', isQuantitySelectable);
            console.log('✅ 버튼 클릭 시 stock-shares 요소:', !!document.getElementById('stock-shares'));
            
            // 수량 선택 가능한 투자상품인 경우 수량 정보 미리 저장
            if (isQuantitySelectable) {
                const sharesInput = document.getElementById('stock-shares');
                if (sharesInput) {
                    card._selectedShares = parseInt(sharesInput.value) || 1;
                    console.log('✅ 선택된 주식 수량 저장:', card._selectedShares);
                } else {
                    console.error('❌ 주식 수량 입력 필드를 찾을 수 없음');
                    this.showModalNotification("오류", "주식 수량을 확인할 수 없습니다.");
                    return;
                }
            }
            
            // 지불 방법 선택이 있는 카드인 경우 선택된 방법 정보 저장
            if (card.paymentOptions && Array.isArray(card.paymentOptions)) {
                const paymentMethodSelect = document.getElementById('payment-method');
                if (paymentMethodSelect) {
                    const selectedIndex = parseInt(paymentMethodSelect.value);
                    const selectedOption = card.paymentOptions[selectedIndex];
                    
                    if (selectedOption) {
                        // 현금 부족 체크
                        const player = this.gameState.player;
                        const requiredCash = selectedOption.downPayment || selectedOption.cost;
                        if (player && requiredCash > player.cash) {
                            this.showModalNotification("알림", `현금이 부족합니다. ${GameUtils.formatCurrency(requiredCash - player.cash)} 더 필요합니다.`);
                            return;
                        }
                        
                        card._selectedPaymentOption = selectedOption;
                        console.log('✅ 선택된 지불 방법 저장:', selectedOption);
                    } else {
                        this.showModalNotification("오류", "지불 방법을 선택해주세요.");
                        return;
                    }
                } else {
                    console.error('❌ 지불 방법 선택 필드를 찾을 수 없음');
                    this.showModalNotification("오류", "지불 방법을 확인할 수 없습니다.");
                    return;
                }
            }
            
            // StockEvent 카드인 경우 선택된 이벤트 유형 저장
            if (isStockEvent) {
                const player = this.gameState.player;
                const stockSymbol = this.extractStockSymbol(card.assetDetails.assetName);
                const hasStock = player && player.stocks && player.stocks[stockSymbol];
                
                if (hasStock) {
                    const stockEventRadios = document.querySelectorAll('input[name="stock-event-type"]');
                    const selectedEventType = Array.from(stockEventRadios).find(radio => radio.checked);
                    
                    if (selectedEventType) {
                        // 카드에 선택된 이벤트 정보 저장
                        card.assetDetails.splitType = selectedEventType.value;
                        card.assetDetails.splitRatio = selectedEventType.value === 'split' ? '2 for 1' : '1 for 2';
                        
                        console.log('선택된 주식 이벤트:', {
                            type: selectedEventType.value,
                            ratio: card.assetDetails.splitRatio
                        });
                    } else {
                        this.showModalNotification("알림", "이벤트 유형을 선택해주세요.");
                        return;
                    }
                }
                // 주식을 보유하지 않은 경우는 그냥 확인만 하고 넘어감
            }
            
            this.hideCardModal();
            
            // StockEvent 카드인 경우 특별 처리
            if (isStockEvent) {
                this.handleStockEvent(card);
            } else {
                this.processCardAction(card);
            }
        };
        
        buttonContainer.appendChild(cancelBtn);
        buttonContainer.appendChild(actionBtn);
        actionsElement.appendChild(buttonContainer);
        
        // 모달 표시
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // 투자상품 UI가 제대로 생성되었는지 최종 확인
        if (isQuantitySelectable) {
            setTimeout(() => {
                const stockSharesInput = document.getElementById('stock-shares');
                console.log('모달 표시 후 stock-shares 확인:', !!stockSharesInput);
                if (!stockSharesInput) {
                    console.error(`❌ ${finalIsStock ? '주식' : '펀드'} UI가 제대로 생성되지 않았습니다!`);
                } else {
                    console.log(`✅ ${finalIsStock ? '주식' : '펀드'} UI 정상 생성됨`);
                }
            }, 100);
        }
        
        // StockEvent 카드인 경우 라디오 버튼 이벤트 리스너 추가
        if (isStockEvent) {
            setTimeout(() => {
                const stockEventRadios = document.querySelectorAll('input[name="stock-event-type"]');
                const titleElement = document.getElementById('card-title');
                const descriptionElement = document.getElementById('card-description');
                
                stockEventRadios.forEach(radio => {
                    radio.addEventListener('change', (e) => {
                        const selectedType = e.target.value;
                        const stockSymbol = this.extractStockSymbol(card.assetDetails.assetName);
                        
                        if (selectedType === 'split') {
                            titleElement.textContent = `주식 이벤트 - ${stockSymbol} 분할`;
                            descriptionElement.innerHTML = `🎉 ${stockSymbol} 회사가 너무 잘되어서 주식이 분할되었습니다!<br><br>보유한 모든 ${stockSymbol} 주식 수량이 2배로 증가하고, 매입가는 절반으로 감소합니다.<br><span class="text-sm text-gray-600">※ 총 자산 가치는 변하지 않습니다.</span>`;
                        } else if (selectedType === 'reverse_split') {
                            titleElement.textContent = `주식 이벤트 - ${stockSymbol} 병합`;
                            descriptionElement.innerHTML = `📉 ${stockSymbol} 회사에 어려움이 발생했습니다!<br><br>보유한 모든 ${stockSymbol} 주식 수량이 절반으로 감소하고, 매입가는 2배로 증가합니다.<br><span class="text-sm text-gray-600">※ 총 자산 가치는 변하지 않습니다.</span>`;
                        }
                    });
                });
                
                // 초기 상태 설정 (분할이 기본 선택)
                const selectedRadio = document.querySelector('input[name="stock-event-type"]:checked');
                if (selectedRadio) {
                    selectedRadio.dispatchEvent(new Event('change'));
                }
            }, 100);
        }
        
        const modalContent = modal.children[0];
        if (modalContent) {
            modalContent.classList.remove('opacity-0', 'scale-95');
            modalContent.classList.add('opacity-100', 'scale-100');
        }
    },

    // 카드 모달 숨기기
    hideCardModal() {
        const modal = document.getElementById('card-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            const modalContent = modal.children[0];
            if (modalContent) {
                modalContent.classList.remove('opacity-100', 'scale-100');
                modalContent.classList.add('opacity-0', 'scale-95');
            }
        }
    },

    // 카드 액션 처리
    processCardAction(card) {
        const player = this.gameState.player;
        if (!player) return;

        // 카드 타입에 따라 다른 처리
        if (card.cardType === 'Doodads' || card.cardType === 'Doodad' || (!card.cardType && card.cost && !card.assetDetails)) {
            this.processDoodadCard(card);
        } else if (card.assetDetails) {
            this.processDealCard(card);
        } else {
            this.addGameLog(`${card.title} 카드를 처리했습니다.`);
            // 기타 카드의 경우에만 UI 업데이트 및 저장
            this.updateUI();
            StorageManager.saveGameState(this.gameState);
        }
    },

    // Doodad 카드 처리 (지불 방법 선택 지원)
    processDoodadCard(card) {
        const player = this.gameState.player;
        if (!player) return;
        
        // 지불 방법이 선택된 경우 선택된 방법으로 처리
        if (card._selectedPaymentOption) {
            const paymentOption = card._selectedPaymentOption;
            
            // 현금 부족 체크 (계약금 또는 총 비용)
            const requiredCash = paymentOption.downPayment || paymentOption.cost;
            
            if (player.cash < requiredCash) {
                this.showModalNotification("알림", "현금이 부족합니다.");
                return;
            }

            // 계약금/초기비용 차감
            player.cash -= requiredCash;
            
            // 대출이 발생하는 경우 부채 추가
            if (paymentOption.debtIncurred && paymentOption.debtIncurred > 0) {
                const debtId = `debt_${Date.now()}`;
                const debt = {
                    id: debtId,
                    name: `${card.title} 대출`,
                    type: "ConsumerLoan",
                    totalAmount: paymentOption.debtIncurred,
                    remainingAmount: paymentOption.debtIncurred,
                    monthlyPayment: paymentOption.monthlyExpenseIncrease || 0,
                    interestRate: 0.10, // 기본 10%
                    isInitial: false
                };
                
                if (!player.liabilities) {
                    player.liabilities = [];
                }
                player.liabilities.push(debt);
                
                // 월 지출 증가
                if (paymentOption.monthlyExpenseIncrease > 0) {
                    player.expenses.other += paymentOption.monthlyExpenseIncrease;
                }
                
                this.addGameLog(`${card.title}을 ${paymentOption.label}로 구매했습니다.`);
                this.addGameLog(`계약금: ${GameUtils.formatCurrency(requiredCash)}, 대출: ${GameUtils.formatCurrency(paymentOption.debtIncurred)}`);
                this.addGameLog(`월 상환액: ${GameUtils.formatCurrency(paymentOption.monthlyExpenseIncrease)} 증가`);
            } else {
                // 현금 일시불인 경우
                this.addGameLog(`${card.title}을 ${paymentOption.label}로 구매했습니다. (${GameUtils.formatCurrency(requiredCash)})`);
            }
            
            // 재무 상태 재계산
            this.recalculatePlayerFinancials();
            
            // 사용된 지불 방법 정보 정리
            delete card._selectedPaymentOption;
            
        } else {
            // 기존 방식 (단순 비용 차감)
            if (player.cash < card.cost) {
                this.showModalNotification("알림", "현금이 부족합니다.");
                return;
            }

            player.cash -= card.cost;
            
            if (card.cashFlowChange && card.cashFlowChange !== 0) {
                player.totalExpenses += Math.abs(card.cashFlowChange);
                this.recalculatePlayerFinancials();
            }

            this.addGameLog(`${card.title}에 ${GameUtils.formatCurrency(card.cost)}를 지출했습니다.`);
        }
        
        // UI 업데이트 및 게임 상태 저장
        this.updateUI();
        StorageManager.saveGameState(this.gameState);
        
        // Doodads 카드 구매 완료 후 대시보드로 이동
        setTimeout(() => {
            this.showTab('dashboard');
        }, 500);
    },

    // 카드를 타이틀별로 그룹화
    groupCardsByTitle(cards) {
        const groups = {};
        cards.forEach(card => {
            const title = card.title;
            if (!groups[title]) {
                groups[title] = [];
            }
            groups[title].push(card);
        });
        return groups;
    },

    // 카드를 카테고리별로 분류
    categorizeCards(cards, cardType) {
        const categories = {};
        
        cards.forEach(card => {
            const category = this.getCardCategory(card, cardType);
            if (!categories[category]) {
                categories[category] = [];
            }
            categories[category].push(card);
        });
        
        return categories;
    },

    // 카드의 카테고리 결정
    getCardCategory(card, cardType) {
        const assetType = card.assetDetails?.assetType;
        
        if (cardType === 'SmallDeals') {
            switch (assetType) {
                case 'Stock':
                case 'StockEvent':
                case 'Investment':
                    return '금융';
                case 'RealEstate':
                    return '부동산';
                case 'Business':
                    return '사업';
                default:
                    return '기타';
            }
        } else if (cardType === 'BigDeals') {
            switch (assetType) {
                case 'RealEstate':
                    return '부동산';
                case 'Business':
                    return '사업';
                case 'Investment':
                    return '투자';
                case 'Stock':
                    return '금융';
                default:
                    return '기타';
            }
        }
        
        return '전체';
    },

    // 카테고리 목록 가져오기
    getCategories(cardType) {
        if (cardType === 'SmallDeals') {
            return ['전체', '금융', '부동산', '사업', '기타'];
        } else if (cardType === 'BigDeals') {
            return ['전체', '부동산', '사업', '투자', '금융', '기타'];
        }
        return ['전체'];
    },

    // 카테고리 탭 렌더링
    renderCategoryTabs(cardType) {
        const tabsContainer = document.getElementById('category-tabs-container');
        const tabsElement = document.getElementById('category-tabs');
        
        if (!tabsContainer || !tabsElement) return;
        
        // 카테고리 탭 표시
        tabsContainer.classList.remove('hidden');
        
        const categories = this.getCategories(cardType);
        tabsElement.innerHTML = '';
        
        categories.forEach(category => {
            const button = document.createElement('button');
            button.className = `category-tab px-4 py-2 rounded-full text-sm font-medium transition-colors ${
                category === '전체' 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            }`;
            button.textContent = category;
            button.dataset.category = category;
            button.dataset.cardType = cardType;
            
            button.addEventListener('click', () => {
                this.selectCategory(cardType, category);
            });
            
            tabsElement.appendChild(button);
        });
    },

    // 카테고리 탭 숨기기
    hideCategoryTabs() {
        const tabsContainer = document.getElementById('category-tabs-container');
        if (tabsContainer) {
            tabsContainer.classList.add('hidden');
        }
    },

    // 카테고리 선택
    selectCategory(cardType, selectedCategory) {
        // 탭 스타일 업데이트
        document.querySelectorAll('.category-tab').forEach(tab => {
            if (tab.dataset.category === selectedCategory) {
                tab.className = 'category-tab px-4 py-2 rounded-full text-sm font-medium transition-colors bg-blue-600 text-white';
            } else {
                tab.className = 'category-tab px-4 py-2 rounded-full text-sm font-medium transition-colors bg-gray-200 text-gray-700 hover:bg-gray-300';
            }
        });

        // 선택된 카테고리의 카드들 표시
        let cards = CARD_DATA[cardType];
        if (cards && cards.length > 0) {
            this.renderCardsByCategory(cards, cardType, selectedCategory);
        }
    },

    // 카테고리별 카드 렌더링
    renderCardsByCategory(cards, cardType, selectedCategory) {
        const cardListContainer = document.getElementById('card-list-container');
        
        // 검색 필터링 먼저 적용
        const searchInput = document.getElementById('card-search-input');
        const searchQuery = searchInput ? searchInput.value : '';
        let filteredCards = this.filterCardsBySearch(cards, searchQuery);
        
        // 선택된 카테고리에 맞는 카드 필터링
        if (selectedCategory !== '전체') {
            filteredCards = filteredCards.filter(card => 
                this.getCardCategory(card, cardType) === selectedCategory
            );
        }
        
        // 검색 결과가 없는 경우 메시지 표시
        if (filteredCards.length === 0) {
            if (searchQuery && searchQuery.trim() !== '') {
                cardListContainer.innerHTML = '<div class="text-center p-4 text-gray-500">검색 결과가 없습니다.</div>';
            } else {
                cardListContainer.innerHTML = '<div class="text-center p-4 text-gray-500">이 카테고리에는 카드가 없습니다.</div>';
            }
            return;
        }
        
        // 카드 그룹화 및 렌더링
        const groupedCards = this.groupCardsByTitle(filteredCards);
        this.renderGroupedCardList(groupedCards, cardListContainer);
    },

    // 그룹화된 카드 목록 렌더링
    renderGroupedCardList(groupedCards, container) {
        container.innerHTML = '';
        
        // 타이틀 순으로 오름차순 정렬
        const sortedEntries = Object.entries(groupedCards).sort(([titleA], [titleB]) => {
            return titleA.localeCompare(titleB, 'ko');
        });
        
        sortedEntries.forEach(([title, cardOptions]) => {
            const cardElement = document.createElement('div');
            cardElement.className = 'card-item p-4 border border-gray-300 rounded-lg hover:shadow-md transition-shadow cursor-pointer bg-white';
            
            cardElement.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">${title}</h3>
                        <p class="text-sm text-blue-600 mb-2">
                            ${(() => {
                                const isStockEventCard = cardOptions.some(card => card.assetDetails && card.assetDetails.assetType === 'StockEvent');
                                if (isStockEventCard) return '주식 이벤트 카드';
                                return cardOptions.length === 1 ? '바로 사용 가능' : `${cardOptions.length}개 옵션 사용 가능`;
                            })()}
                        </p>
                        <p class="text-sm text-gray-600">
                            ${(() => {
                                const isStockEventCard = cardOptions.some(card => card.assetDetails && card.assetDetails.assetType === 'StockEvent');
                                if (isStockEventCard) return '클릭하여 분할/병합을 선택하세요';
                                return cardOptions.length === 1 ? '클릭하여 상세 정보를 확인하세요' : '클릭하여 옵션을 확인하세요';
                            })()}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">가격 범위</div>
                        <div class="text-sm font-medium text-gray-800">
                            ${this.getPriceRange(cardOptions)}
                        </div>
                    </div>
                </div>
            `;
            
            cardElement.addEventListener('click', () => {
                // StockEvent 카드인 경우 바로 첫 번째 카드의 모달 표시 (분할/병합 선택 UI 제공)
                const isStockEventCard = cardOptions.some(card => card.assetDetails && card.assetDetails.assetType === 'StockEvent');
                
                if (isStockEventCard) {
                    // StockEvent는 바로 첫 번째 카드로 모달 열기
                    this.showCardModal(cardOptions[0]);
                } else if (cardOptions.length === 1) {
                    // 옵션이 하나만 있는 경우 바로 카드 모달 표시
                    this.showCardModal(cardOptions[0]);
                } else {
                    // 여러 옵션이 있는 경우 그룹 모달 표시
                    this.showGroupedCardModal(title, cardOptions);
                }
            });
            
            container.appendChild(cardElement);
        });
    },

    // 카드 옵션들의 가격 범위 구하기
    getPriceRange(cardOptions) {
        const prices = cardOptions.map(card => card.cost || card.downPayment || 0).filter(price => price > 0);
        if (prices.length === 0) return '무료';
        
        const minPrice = Math.min(...prices);
        const maxPrice = Math.max(...prices);
        
        if (minPrice === maxPrice) {
            return GameUtils.formatCurrency(minPrice);
        } else {
            return `${GameUtils.formatCurrency(minPrice)} ~ ${GameUtils.formatCurrency(maxPrice)}`;
        }
    },

    // 그룹화된 카드 모달 표시
    showGroupedCardModal(title, cardOptions) {
        const modal = document.getElementById('card-modal');
        const titleElement = document.getElementById('card-title');
        const descriptionElement = document.getElementById('card-description');
        const detailsInputsElement = document.getElementById('card-details-inputs');
        const actionsElement = document.getElementById('card-actions');

        if (!modal || !titleElement || !descriptionElement || !detailsInputsElement || !actionsElement) {
            console.error('카드 모달 요소를 찾을 수 없습니다.');
            return;
        }

        // 모달 제목 설정
        titleElement.textContent = title;
        descriptionElement.textContent = `${cardOptions.length}개의 옵션 중 하나를 선택하세요.`;

        // 카드 옵션을 비용 오름차순으로 정렬
        const sortedCardOptions = [...cardOptions].sort((a, b) => {
            // 무조건 cost 기준으로 정렬
            const costA = a.cost || 0;
            const costB = b.cost || 0;
            return costA - costB;
        });

        // 원래 인덱스를 추적하기 위한 매핑 생성
        const originalIndexMap = new Map();
        sortedCardOptions.forEach((sortedCard, sortedIndex) => {
            const originalIndex = cardOptions.findIndex(card => card === sortedCard);
            originalIndexMap.set(sortedIndex, originalIndex);
        });

        // 옵션 선택 UI 생성
        let optionsHTML = `
            <div class="mb-4">
                <label for="card-option-select" class="block text-sm font-medium text-gray-700 mb-2">옵션 선택</label>
                <select id="card-option-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">옵션을 선택하세요</option>
                    ${sortedCardOptions.map((card, sortedIndex) => {
                        const cost = card.cost || 0;
                        const downPayment = card.downPayment || 0;
                        
                        let priceText = '';
                        if (cost > 0 && downPayment > 0) {
                            // 비용과 계약금이 모두 있는 경우
                            priceText = `비용: ${GameUtils.formatCurrency(cost)}, 계약금: ${GameUtils.formatCurrency(downPayment)}`;
                        } else if (cost > 0) {
                            // 비용만 있는 경우
                            priceText = `비용: ${GameUtils.formatCurrency(cost)}`;
                        } else if (downPayment > 0) {
                            // 계약금만 있는 경우
                            priceText = `계약금: ${GameUtils.formatCurrency(downPayment)}`;
                        } else {
                            // 무료인 경우
                            priceText = '무료';
                        }
                        
                        // option value에는 원래 인덱스를 저장
                        const originalIndex = originalIndexMap.get(sortedIndex);
                        return `<option value="${originalIndex}">${priceText}</option>`;
                    }).join('')}
                </select>
            </div>
            <div id="selected-card-details" class="min-h-[100px] p-3 bg-gray-50 rounded border">
                <p class="text-gray-500 text-center">옵션을 선택하면 상세 정보가 표시됩니다.</p>
            </div>
        `;

        detailsInputsElement.innerHTML = optionsHTML;

        // 옵션 선택 이벤트 리스너
        const optionSelect = document.getElementById('card-option-select');
        const detailsDiv = document.getElementById('selected-card-details');
        
        let selectedCard = null;

        const updateCardDetails = () => {
            const selectedIndex = optionSelect.value;
            if (selectedIndex === '') {
                detailsDiv.innerHTML = '<p class="text-gray-500 text-center">옵션을 선택하면 상세 정보가 표시됩니다.</p>';
                selectedCard = null;
                return;
            }

            selectedCard = cardOptions[parseInt(selectedIndex)];
            
            let detailsHTML = `
                <div class="space-y-2">
                    <h4 class="font-medium text-gray-800">${selectedCard.title}</h4>
                    <p class="text-sm text-gray-600">${selectedCard.description}</p>
                    
                    <div class="pt-2 border-t border-gray-200 space-y-1">
                        ${selectedCard.cost ? `<div class="flex justify-between text-sm"><span>비용:</span><span class="font-medium">${GameUtils.formatCurrency(selectedCard.cost)}</span></div>` : ''}
                        ${selectedCard.downPayment ? `<div class="flex justify-between text-sm"><span>계약금:</span><span class="font-medium">${GameUtils.formatCurrency(selectedCard.downPayment)}</span></div>` : ''}
                        ${selectedCard.debtIncurred ? `<div class="flex justify-between text-sm"><span>대출:</span><span class="font-medium text-red-600">${GameUtils.formatCurrency(selectedCard.debtIncurred)}</span></div>` : ''}
                        ${selectedCard.cashFlowChange ? `<div class="flex justify-between text-sm"><span>월 현금흐름:</span><span class="font-medium ${selectedCard.cashFlowChange >= 0 ? 'text-green-600' : 'text-red-600'}">${selectedCard.cashFlowChange >= 0 ? '+' : ''}${GameUtils.formatCurrency(selectedCard.cashFlowChange)}</span></div>` : ''}
                    </div>
                </div>
            `;
            
            detailsDiv.innerHTML = detailsHTML;
        };

        optionSelect.addEventListener('change', updateCardDetails);

        // 액션 버튼 생성
        actionsElement.innerHTML = '';
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'flex space-x-3';

        // 취소 버튼
        const cancelBtn = document.createElement('button');
        cancelBtn.className = 'flex-1 bg-gray-300 text-gray-800 py-2.5 rounded-lg font-semibold hover:bg-gray-400 text-sm';
        cancelBtn.textContent = '취소';
        cancelBtn.onclick = () => this.hideCardModal();

        // 구매/투자 버튼
        const actionBtn = document.createElement('button');
        actionBtn.className = 'flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 text-sm';
        actionBtn.textContent = '선택하기';
        actionBtn.onclick = () => {
            if (!selectedCard) {
                this.showModalNotification("알림", "옵션을 선택해주세요.");
                return;
            }
            
            this.hideCardModal();
            this.showCardModal(selectedCard);
        };

        buttonContainer.appendChild(cancelBtn);
        buttonContainer.appendChild(actionBtn);
        actionsElement.appendChild(buttonContainer);

        // 모달 표시
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        const modalContent = modal.children[0];
        if (modalContent) {
            modalContent.classList.remove('opacity-0', 'scale-95');
            modalContent.classList.add('opacity-100', 'scale-100');
        }
    },

    // tradingRange 정보 파싱 및 표시
    parseTradingRange(tradingRange, currentPrice, isStock, isFund) {
        if (!tradingRange || typeof tradingRange !== 'string') {
            return '';
        }

        // "10 ~ 30" 또는 "1200 ~ 1200" 형태의 범위 파싱
        const rangeMatch = tradingRange.match(/(\d+)\s*~\s*(\d+)/);
        if (!rangeMatch) {
            return `<div class="text-sm mt-2 p-2 bg-gray-50 rounded border"><strong>가격 범위:</strong> ${tradingRange}</div>`;
        }

        const minPrice = parseFloat(rangeMatch[1]);
        const maxPrice = parseFloat(rangeMatch[2]);
        
        // 현재 가격이 범위에서 어느 위치에 있는지 계산 (0.0 ~ 1.0)
        let pricePosition = 0;
        if (maxPrice > minPrice) {
            pricePosition = (currentPrice - minPrice) / (maxPrice - minPrice);
        } else {
            // 최소/최대가 같은 경우 (고정 가격)
            pricePosition = 0.5;
        }

        // 가격 수준 및 색상 결정
        let priceLevel = '';
        let levelColor = '';
        let investmentAdvice = '';

        if (maxPrice === minPrice) {
            // 고정 가격인 경우
            priceLevel = '고정가';
            levelColor = 'text-blue-600';
            investmentAdvice = '안정적인 투자';
        } else if (pricePosition <= 0.2) {
            priceLevel = '최저가';
            levelColor = 'text-green-600';
            investmentAdvice = '🔥 최고의 매수 기회!';
        } else if (pricePosition <= 0.4) {
            priceLevel = '저가';
            levelColor = 'text-green-500';
            investmentAdvice = '좋은 매수 기회!';
        } else if (pricePosition <= 0.6) {
            priceLevel = '중저가';
            levelColor = 'text-yellow-600';
            investmentAdvice = '적정 매수 타이밍';
        } else if (pricePosition <= 0.8) {
            priceLevel = '중고가';
            levelColor = 'text-orange-500';
            investmentAdvice = '신중한 검토 필요';
        } else {
            priceLevel = '최고가';
            levelColor = 'text-red-500';
            investmentAdvice = '높은 가격대';
        }

        const assetType = isStock ? '주식' : (isFund ? '펀드' : '자산');
        
        return `
            <div class="text-sm mt-2 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-gray-700">📊 ${assetType} 가격 분석</span>
                    <span class="${levelColor} font-bold">${priceLevel}</span>
                </div>
                
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">거래 범위:</span>
                        <span class="font-medium">${GameUtils.formatCurrency(minPrice)} ~ ${GameUtils.formatCurrency(maxPrice)}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">현재 가격:</span>
                        <span class="font-medium text-gray-800">${GameUtils.formatCurrency(currentPrice)}</span>
                    </div>
                    
                    <!-- 가격 위치 시각화 바 -->
                    ${maxPrice > minPrice ? `
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-1">
                            <div class="bg-gradient-to-r from-green-400 via-yellow-400 to-red-400 h-2 rounded-full" style="width: 100%;"></div>
                            <div class="relative">
                                <div class="absolute bg-gray-800 w-1 h-3 rounded-sm -mt-2.5" 
                                     style="left: ${pricePosition * 100}%; transform: translateX(-50%);"></div>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>저가</span>
                            <span>고가</span>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="mt-2 pt-2 border-t border-blue-200">
                        <div class="text-xs font-medium ${levelColor.replace('text-', 'text-')}">
                            💡 ${investmentAdvice}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
});