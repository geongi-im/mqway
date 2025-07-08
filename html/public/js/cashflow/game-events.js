// 이벤트 핸들러 및 게임 액션 함수들

// CashflowGame 클래스에 이벤트 및 액션 관련 메서드들 추가
Object.assign(CashflowGame.prototype, {
    
    // 이벤트 바인딩
    bindEvents() {
        // DOM 요소가 존재하는지 확인하는 헬퍼 함수
        const safeAddEventListener = (elementId, event, handler) => {
            const element = document.getElementById(elementId);
            if (element) {
                element.addEventListener(event, handler);
            } else {
                console.warn(`Element with ID '${elementId}' not found`);
            }
        };

        safeAddEventListener('start-game-btn', 'click', () => {
            const playerNameEl = document.getElementById('player-name');
            const startGameBtnEl = document.getElementById('start-game-btn');
            const playerName = playerNameEl ? playerNameEl.value.trim() : '';
            const selectedProfessionIndex = startGameBtnEl ? startGameBtnEl.dataset.selectedProfession : undefined;
            if (!playerName) { this.showModalNotification("알림", "플레이어 이름을 입력해주세요."); return; }
            if (selectedProfessionIndex === undefined) { this.showModalNotification("알림", "직업을 선택해주세요."); return; }
            this.startGame(playerName, parseInt(selectedProfessionIndex));
        });



        // 이름, 꿈, 꿈 비용 입력 이벤트
        safeAddEventListener('player-name', 'input', (e) => {
            this.updateStartButtonState();
        });
        
        safeAddEventListener('player-dream', 'input', (e) => {
            // 꿈이 변경되어도 시작 버튼 상태는 영향 없음
        });
        
        safeAddEventListener('dream-cost', 'input', async (e) => {
            // 꿈 비용이 유효한지 확인 (음수 체크)
            const value = parseFloat(e.target.value);
            if (value < 0) {
                e.target.value = 0;
                return;
            }
            
            // USD to KRW 변환 표시
            await this.updateDreamCostKrw(value);
        });

        // 카드 검색 입력 이벤트
        safeAddEventListener('card-search-input', 'input', (e) => {
            // 현재 선택된 카드 타입을 가져와서 다시 렌더링
            const cardTypeSelect = document.getElementById('card-type-select');
            if (cardTypeSelect && cardTypeSelect.value) {
                this.renderCardList(cardTypeSelect.value);
            }
        });

        // 카드 선택 버튼에 이벤트 리스너 추가 - 카드목록 페이지로 이동
        safeAddEventListener('smalldeal-btn', 'click', () => this.goToCardListPage('SmallDeals'));
        safeAddEventListener('bigdeal-btn', 'click', () => this.goToCardListPage('BigDeals'));
        safeAddEventListener('doodad-btn', 'click', () => this.goToCardListPage('Doodads'));
        
        // 출산 버튼에 이벤트 리스너 추가
        safeAddEventListener('have-child-btn', 'click', () => {
            const player = this.gameState.player;
            if (!player) return;
            
            if (player.expenses.childrenCount >= 3) {
                this.showModalNotification("알림", "최대 자녀 수(3명)에 도달했습니다.");
                return;
            }
            
            const currentChildren = player.expenses.childrenCount || 0;
            const monthlyChildExpense = player.expenses.perChildExpense || 200;
            
            this.showModalNotification(
                "출산하기",
                `자녀를 출산하시겠습니까?\n\n현재 자녀 수: ${currentChildren}명\n출산 후: ${currentChildren + 1}명\n\n월 양육비가 ${GameUtils.formatCurrency(monthlyChildExpense)} 증가합니다.\n\n계속하시겠습니까?`,
                () => this.handleChildBirth(),
                true // 취소 버튼 표시
            );
        });
        
        // 월급 받기 버튼
        safeAddEventListener('payday-btn', 'click', () => {
            this.showModalNotification(
                "월급 받기",
                "월급을 받으시겠습니까?",
                () => this.handlePayday(),
                true // 취소 버튼 표시
            );
        });
        
        // 기부 버튼
        safeAddEventListener('charity-btn', 'click', () => {
            const player = this.gameState.player;
            if (!player) return;
            
            // 총수입 계산
            const salary = player.salary || 0;
            const passiveIncome = player.passiveIncome || 0;
            const totalIncome = salary + passiveIncome;
            const charityAmount = Math.floor(totalIncome * 0.1);
            
            if (totalIncome < 1000) {
                this.showModalNotification("알림", "기부하려면 최소 $1,000의 총수입이 필요합니다.");
                return;
            }
            
            if (player.cash < charityAmount) {
                this.showModalNotification("알림", `기부할 현금이 부족합니다.\n\n기부 금액: ${GameUtils.formatCurrency(charityAmount)} (총수입의 10%)\n보유 현금: ${GameUtils.formatCurrency(player.cash)}`);
                return;
            }
            
            setTimeout(() => {
                this.showModalNotification(
                    "기부하기",
                    `총수입의 10%인 ${GameUtils.formatCurrency(charityAmount)}을 기부하시겠습니까?\n\n총수입: ${GameUtils.formatCurrency(totalIncome)} (급여 ${GameUtils.formatCurrency(salary)} + 자산소득 ${GameUtils.formatCurrency(passiveIncome)})\n\n기부를 하시면 3턴 동안 주사위를 하나 더 굴릴 수 있는 행운이 따를 것입니다.`,
                    () => this.handleCharity(),
                    true // 취소 버튼 표시
                );
            }, 200);
        });
        
        // 실직 버튼
        safeAddEventListener('downsized-btn', 'click', () => {
            const player = this.gameState.player;
            if (!player) return;
            
            // 총 지출 계산 (실제 handleDownsized와 동일한 로직으로 미리 계산)
            const expenses = {
                taxes: player.expenses.taxes || 0,
                homeMortgage: player.expenses.homePayment || 0,
                schoolLoan: player.expenses.schoolLoan || 0,
                carLoan: player.expenses.carLoan || 0,
                creditCard: player.expenses.creditCard || 0,
                retail: player.expenses.retail || 0,
                other: player.expenses.other || 0,
                children: player.expenses.children || 0
            };
            
            let additionalDebtPayments = 0;
            if (player.liabilities && player.liabilities.length > 0) {
                additionalDebtPayments = player.liabilities
                    .filter(debt => !debt.isInitial)
                    .reduce((sum, debt) => sum + (debt.monthlyPayment || 0), 0);
            }
            
            let emergencyLoanPayments = 0;
            if (player.emergencyLoans && player.emergencyLoans.length > 0) {
                emergencyLoanPayments = player.emergencyLoans.reduce((sum, loan) => sum + (loan.monthlyPayment || 0), 0);
            }
            
            const totalFixedExpenses = Object.values(expenses).reduce((sum, exp) => sum + exp, 0);
            const totalAdditionalPayments = additionalDebtPayments + emergencyLoanPayments;
            const penaltyAmount = totalFixedExpenses + totalAdditionalPayments;
            
            let message = `실직 상태가 되면 월 지출액(${GameUtils.formatCurrency(penaltyAmount)})을 지불해야 하며, 한 턴을 건너뛰게 됩니다.\n\n`;
            
            if (player.cash < penaltyAmount) {
                message += "현금이 부족할 경우 대출을 받거나 자산을 매각해야 합니다.\n\n";
            }
            
            message += "계속하시겠습니까?";
            
            this.showModalNotification(
                "실직 위기",
                message,
                () => this.handleDownsized(),
                true // 취소 버튼 표시
            );
        });
        
        // 긴급 대출 버튼
        safeAddEventListener('emergency-loan-btn', 'click', () => {
            this.showEmergencyLoanModal();
        });

        // 카드 타입 선택 드롭다운 이벤트
        safeAddEventListener('card-type-select', 'change', (e) => {
            this.renderCardList(e.target.value);
        });

        // 카드목록 페이지 뒤로가기 버튼
        safeAddEventListener('back-to-dashboard-btn', 'click', () => {
            this.showTab('dashboard');
        });

        // 판매 관련 이벤트
        safeAddEventListener('confirm-sell-asset-btn', 'click', () => {
            const asset = this.gameState.currentSellingAsset;
            const assetType = this.gameState.currentSellingAssetType;
            
            if (!asset) {
                this.showModalNotification("오류", "판매할 자산 정보를 찾을 수 없습니다.");
                return;
            }
            
            if (asset.type === 'Stock' || asset.type === 'Fund') {
                // 주식/펀드 판매 처리
                const sellSharesInput = document.getElementById('sell-shares');
                const sellPricePerShareInput = document.getElementById('sell-price-per-share');
                
                if (!sellSharesInput || !sellPricePerShareInput) {
                    this.showModalNotification("오류", "판매 정보 입력 필드를 찾을 수 없습니다.");
                    return;
                }
                
                const sellShares = parseInt(sellSharesInput.value);
                const sellPricePerShare = parseFloat(sellPricePerShareInput.value);
                
                if (isNaN(sellShares) || sellShares <= 0 || sellShares > asset.shares) {
                    this.showModalNotification("오류", `유효한 판매 수량을 입력하세요. (1 ~ ${asset.shares})`);
                    return;
                }
                
                if (isNaN(sellPricePerShare) || sellPricePerShare < 0) {
                    const unit = asset.type === 'Fund' ? '좌당' : '주당';
                    this.showModalNotification("오류", `유효한 ${unit} 판매 가격을 입력하세요.`);
                    return;
                }
                
                const totalSellAmount = sellShares * sellPricePerShare;
                this.processSellAsset(this.gameState.currentSellingAssetId, totalSellAmount, sellShares, sellPricePerShare);
            } else {
                // 일반 자산 판매 처리
                const sellPriceInput = document.getElementById('sell-price');
                if (!sellPriceInput) return;
                
                const sellPrice = parseFloat(sellPriceInput.value);
                if (isNaN(sellPrice) || sellPrice < 0) {
                    this.showModalNotification("오류", "유효한 판매 가격을 입력하세요.");
                    return;
                }
                
                // 부동산은 모기지론을 이용하므로 별도의 손실 확인 없이 차액만 처리
                
                this.processSellAsset(this.gameState.currentSellingAssetId, sellPrice);
            }
            
            this.hideSellAssetModal();
        });
        
        safeAddEventListener('cancel-sell-asset-btn', 'click', () => this.hideSellAssetModal());
    },

    // 네비게이션 이벤트 바인딩
    bindNavigationEvents() {
        const navButtons = document.querySelectorAll('.nav-btn');
        navButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const target = e.currentTarget.dataset.tab;
                this.showTab(target);
            });
        });

        // 카드 타입 선택 이벤트
        document.getElementById('card-type-select').addEventListener('change', (e) => {
            this.renderCardList(e.target.value);
        });
    },

    // 탭 표시
    showTab(tabName) {
        // 모든 탭 숨기기 (CSS 클래스와 display 스타일 모두 적용)
        const allTabs = document.querySelectorAll('.tab-content');
        allTabs.forEach(tab => {
            tab.classList.add('hidden');
            tab.style.display = 'none';
        });
        
        // 선택된 탭 표시 (CSS 클래스와 display 스타일 모두 적용)
        const selectedTab = document.getElementById(`tab-content-${tabName}`);
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
            selectedTab.style.display = 'block';
        }
        
        // 네비게이션 버튼 상태 업데이트
        const navButtons = document.querySelectorAll('.nav-btn');
        navButtons.forEach(btn => {
            btn.classList.remove('text-blue-600', 'bg-blue-50');
        });
        
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeButton) {
            activeButton.classList.add('text-blue-600', 'bg-blue-50');
        }

        // 대시보드 탭이 선택되면 차트 업데이트
        if (tabName === 'dashboard') {
            setTimeout(() => {
                this.updateCashflowGaugeChart();
                if (this.ratRaceChart) {
                    this.ratRaceChart.resize();
                }
            }, 100);
        }

        // 카드 탭이 선택되면 카드 목록 렌더링
        if (tabName === 'cards') {
            const cardTypeSelect = document.getElementById('card-type-select');
            if (cardTypeSelect) {
                // 기본값이 없으면 첫 번째 옵션으로 설정
                if (!cardTypeSelect.value) {
                    cardTypeSelect.value = 'SmallDeals';
                }
                this.renderCardList(cardTypeSelect.value);
            }
        }
    },

    // 스크롤 핸들러 설정
    setupScrollHandler() {
        // 스크롤 이벤트는 필요한 경우에만 구현
    },

    // 월급 처리 (script.js.backup 기반)
    handlePayday() {
        const player = this.gameState.player;
        if (!player) return;
        
        // 게임 종료 상태 체크
        if (this.gameState.gameEnded) {
            this.showModalNotification("게임 종료", "게임이 이미 종료되었습니다.");
            return;
        }

        try {
            // 급여 계산 (직업 데이터에서 가져오기)
            const salary = player.salary;
            
            // 지출 카테고리 세분화
            const expenses = {
                taxes: player.expenses.taxes || 0,
                homeMortgage: player.expenses.homePayment || 0,
                schoolLoan: player.expenses.schoolLoan || 0,
                carLoan: player.expenses.carLoan || 0,
                creditCard: player.expenses.creditCard || 0,
                retail: player.expenses.retail || 0,
                other: player.expenses.other || 0,
                children: player.expenses.children || 0
            };

            // 추가 부채 상환액 계산 (초기 부채 제외, 게임 중 새로 생긴 부채만)
            let additionalDebtPayments = 0;
            if (player.liabilities && player.liabilities.length > 0) {
                additionalDebtPayments = player.liabilities
                    .filter(debt => !debt.isInitial) // 초기 부채가 아닌 것만
                    .reduce((sum, debt) => sum + (debt.monthlyPayment || 0), 0);
            }

            // 긴급 대출 이자 계산
            let emergencyLoanPayments = 0;
            if (player.emergencyLoans && player.emergencyLoans.length > 0) {
                emergencyLoanPayments = player.emergencyLoans.reduce((sum, loan) => sum + (loan.monthlyPayment || 0), 0);
            }

            // 총 지출 계산 (기본 지출 + 추가 부채 + 긴급 대출)
            const totalFixedExpenses = Object.values(expenses).reduce((sum, exp) => sum + exp, 0);
            const totalAdditionalPayments = additionalDebtPayments + emergencyLoanPayments;
            const totalExpenses = totalFixedExpenses + totalAdditionalPayments;

            // 패시브 인컴 추가
            const passiveIncome = player.passiveIncome || 0;
            const totalIncome = salary + passiveIncome;

            // 순 현금흐름 계산
            const netCashFlow = totalIncome - totalExpenses;

            // 현금에 순 현금흐름 추가 (숫자 타입 보장)
            player.cash = parseFloat(player.cash || 0) + netCashFlow;

            // 로그 엔트리 생성 - 상세 지출 내역 포함
            const expenseDetails = [];
            if (expenses.taxes > 0) expenseDetails.push(`세금: ${GameUtils.formatCurrency(expenses.taxes)}`);
            if (expenses.homeMortgage > 0) expenseDetails.push(`주택 대출: ${GameUtils.formatCurrency(expenses.homeMortgage)}`);
            if (expenses.schoolLoan > 0) expenseDetails.push(`학자금 대출: ${GameUtils.formatCurrency(expenses.schoolLoan)}`);
            if (expenses.carLoan > 0) expenseDetails.push(`자동차 대출: ${GameUtils.formatCurrency(expenses.carLoan)}`);
            if (expenses.creditCard > 0) expenseDetails.push(`신용카드: ${GameUtils.formatCurrency(expenses.creditCard)}`);
            if (expenses.retail > 0) expenseDetails.push(`소매 지출: ${GameUtils.formatCurrency(expenses.retail)}`);
            if (expenses.other > 0) expenseDetails.push(`기타 지출: ${GameUtils.formatCurrency(expenses.other)}`);
            if (expenses.children > 0) expenseDetails.push(`자녀 양육비: ${GameUtils.formatCurrency(expenses.children)}`);
            if (totalAdditionalPayments > 0) expenseDetails.push(`추가 부채 상환: ${GameUtils.formatCurrency(totalAdditionalPayments)}`);

            // 게임 로그에 세부 내역과 함께 추가
            this.addGameLog(`월급날: 급여 ${GameUtils.formatCurrency(salary)} 수령${passiveIncome > 0 ? `, 자산소득 ${GameUtils.formatCurrency(passiveIncome)}` : ''} - 총 지출액 ${GameUtils.formatCurrency(totalExpenses)}`, 'expense', null, expenseDetails);

            // 재무 상태 재계산
            this.recalculatePlayerFinancials();

            // 파산 체크 - 현금이 마이너스이고 현금흐름도 마이너스인 경우
            if (player.cash < 0 && netCashFlow < 0) {
                console.log('파산 위험 상황 감지:', {
                    cash: player.cash,
                    netCashFlow: netCashFlow,
                    totalAssets: this.calculateTotalAssetValue()
                });
                
                const bankruptcyResult = this.handleBankruptcy();
                if (bankruptcyResult.isBankrupt) {
                    // 파산 처리됨 - 더 이상 진행하지 않음
                    return;
                }
            }

            // UI 업데이트
            this.updateUI();
            StorageManager.saveGameState(this.gameState);

            // 결과 메시지 생성
            let messageText = `월급 ${GameUtils.formatCurrency(salary)}을 받았습니다!`;
            if (passiveIncome > 0) {
                messageText += `\n자산 소득 ${GameUtils.formatCurrency(passiveIncome)}도 받았습니다!`;
            }
            messageText += `\n\n월별 지출 ${GameUtils.formatCurrency(totalExpenses)}을 차감했습니다.`;
            messageText += `\n순 현금흐름: ${netCashFlow >= 0 ? '+' : ''}${GameUtils.formatCurrency(netCashFlow)}`;
            messageText += `\n현재 현금: ${GameUtils.formatCurrency(player.cash)}`;

            // 모달이 완전히 닫힌 후 결과 표시
            setTimeout(() => {
                this.showModalNotification("월급날!", messageText);
            }, 200);

        } catch (error) {
            console.error("월급 처리 중 오류:", error);
            this.addGameLog("월급 처리 중 오류가 발생했습니다.", 'error');
        }
    },

    // 기부 처리 (script.js.backup 기반)
    handleCharity() {
        const player = this.gameState.player;
        if (!player) return;
        
        // 게임 종료 상태 체크
        if (this.gameState.gameEnded) {
            this.showModalNotification("게임 종료", "게임이 이미 종료되었습니다.");
            return;
        }

        try {
            // 총수입 계산 (급여 + 패시브 인컴)
            const salary = player.salary || 0;
            const passiveIncome = player.passiveIncome || 0;
            const totalIncome = salary + passiveIncome;
            const charityAmount = Math.floor(totalIncome * 0.1);
            
            if (charityAmount < 100) {
                this.showModalNotification("알림", "기부하려면 최소 $1,000의 총수입이 필요합니다. (기부 금액은 총수입의 10%)");
                return;
            }
            
            if (player.cash < charityAmount) {
                this.showModalNotification("알림", `기부할 현금이 부족합니다.\n\n기부 금액: ${GameUtils.formatCurrency(charityAmount)} (총수입의 10%)\n보유 현금: ${GameUtils.formatCurrency(player.cash)}`);
                return;
            }

            // 현금에서 기부 금액 차감 (숫자 타입 보장)
            player.cash = parseFloat(player.cash || 0) - charityAmount;

            // 게임 로그 추가
            this.addGameLog(`기부: ${GameUtils.formatCurrency(charityAmount)}을 기부했습니다. 행운이 따를 것입니다!`, 'event-positive');

            // 재무 상태 재계산
            this.recalculatePlayerFinancials();

            // UI 업데이트
            this.updateUI();
            
            // 전체 게임 상태 저장
            StorageManager.saveGameState(this.gameState);

            // 완료 메시지
            setTimeout(() => {
                this.showModalNotification(
                    "기부 완료!",
                    `총수입의 10%인 ${GameUtils.formatCurrency(charityAmount)}을 기부했습니다.\n\n총수입: ${GameUtils.formatCurrency(totalIncome)} (급여 ${GameUtils.formatCurrency(salary)} + 자산소득 ${GameUtils.formatCurrency(passiveIncome)})\n\n기부를 하셨으므로 3턴 동안 주사위를 하나 더 굴릴 수 있는 행운이 따를 것입니다!\n\n현재 현금: ${GameUtils.formatCurrency(player.cash)}`
                );
            }, 200);

        } catch (error) {
            console.error("기부 처리 중 오류:", error);
            this.addGameLog("기부 처리 중 오류가 발생했습니다.", 'error');
        }
    },

    // 실직 처리 (script.js.backup 기반)
    handleDownsized() {
        const player = this.gameState.player;
        if (!player) return;
        
        // 게임 종료 상태 체크
        if (this.gameState.gameEnded) {
            this.showModalNotification("게임 종료", "게임이 이미 종료되었습니다.");
            return;
        }

        try {
            // 총 지출 계산 (월급날 처리와 동일한 로직)
            const expenses = {
                taxes: player.expenses.taxes || 0,
                homeMortgage: player.expenses.homePayment || 0,
                schoolLoan: player.expenses.schoolLoan || 0,
                carLoan: player.expenses.carLoan || 0,
                creditCard: player.expenses.creditCard || 0,
                retail: player.expenses.retail || 0,
                other: player.expenses.other || 0,
                children: player.expenses.children || 0
            };

            // 추가 부채 상환액 계산 (초기 부채 제외, 게임 중 새로 생긴 부채만)
            let additionalDebtPayments = 0;
            if (player.liabilities && player.liabilities.length > 0) {
                additionalDebtPayments = player.liabilities
                    .filter(debt => !debt.isInitial) // 초기 부채가 아닌 것만
                    .reduce((sum, debt) => sum + (debt.monthlyPayment || 0), 0);
            }

            // 긴급 대출 이자 계산
            let emergencyLoanPayments = 0;
            if (player.emergencyLoans && player.emergencyLoans.length > 0) {
                emergencyLoanPayments = player.emergencyLoans.reduce((sum, loan) => sum + (loan.monthlyPayment || 0), 0);
            }

            // 총 지출 계산 (기본 지출 + 추가 부채 + 긴급 대출)
            const totalFixedExpenses = Object.values(expenses).reduce((sum, exp) => sum + exp, 0);
            const totalAdditionalPayments = additionalDebtPayments + emergencyLoanPayments;
            const penaltyAmount = totalFixedExpenses + totalAdditionalPayments; // 총 지출 1회치
            
            if (player.cash < penaltyAmount) {
                // 현금이 부족한 경우 자산/부채 탭으로 이동하여 자산 매각 또는 대출 유도
                setTimeout(() => {
                    this.showModalNotification(
                        "현금 부족",
                        `실직 페널티 ${GameUtils.formatCurrency(penaltyAmount)}을 지불하기 위한 현금이 부족합니다.\n\n현재 현금: ${GameUtils.formatCurrency(player.cash)}\n부족 금액: ${GameUtils.formatCurrency(penaltyAmount - player.cash)}\n\n자산을 매각하거나 긴급 대출을 받아 현금을 확보한 후 다시 시도하세요.`,
                        () => {
                            // 자산/부채 탭으로 이동
                            this.showTab('assets');
                        }
                    );
                }, 200);
                return;
            }

            // 현금에서 페널티 차감
            player.cash -= penaltyAmount;

            // 게임 로그 추가
            this.addGameLog(`실직: 월 지출액 ${GameUtils.formatCurrency(penaltyAmount)}을 지불했습니다.`, 'event-negative');
            this.addGameLog("실직으로 인해 한 턴을 건너뛰게 됩니다.", 'info');

            // 재무 상태 재계산
            this.recalculatePlayerFinancials();

            // UI 업데이트
            this.updateUI();
            StorageManager.saveGameState(this.gameState);

            // 완료 메시지
            setTimeout(() => {
                this.showModalNotification(
                    "실직 처리 완료",
                    `월 지출액 ${GameUtils.formatCurrency(penaltyAmount)}을 지불했습니다.\n\n실직으로 인해 한 턴을 건너뛰게 됩니다.\n\n현재 현금: ${GameUtils.formatCurrency(player.cash)}`
                );
            }, 200);

        } catch (error) {
            console.error("실직 처리 중 오류:", error);
            this.addGameLog("실직 처리 중 오류가 발생했습니다.", 'error');
        }
    },

    // 긴급 대출 모달 표시 (버튼 기반)
    showEmergencyLoanModal() {
        console.log('긴급 대출 모달 열기 시도');
        const modal = document.getElementById('emergency-loan-modal');
        const confirmBtn = document.getElementById('confirm-loan-btn');
        const cancelBtn = document.getElementById('cancel-loan-btn');
        const selectedAmountDisplay = document.getElementById('selected-loan-amount');
        const interestPreview = document.getElementById('monthly-interest-preview');
        
        if (!modal || !confirmBtn || !cancelBtn || !selectedAmountDisplay) {
            console.error('긴급 대출 모달 요소를 찾을 수 없습니다.', {
                modal: !!modal,
                confirmBtn: !!confirmBtn,
                cancelBtn: !!cancelBtn,
                selectedAmountDisplay: !!selectedAmountDisplay
            });
            return;
        }
        console.log('모든 모달 요소 찾음, 모달 표시 진행');
        
        // 현재 선택된 대출 금액 (전역 변수로 관리)
        let currentLoanAmount = 0;
        
        // 확인 버튼 클론 생성 (이벤트 처리를 위해 먼저 생성)
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // 선택된 금액과 이자 미리보기 업데이트
        const updateDisplay = () => {
            selectedAmountDisplay.textContent = GameUtils.formatCurrency(currentLoanAmount);
            const monthlyInterest = Math.round(currentLoanAmount * GAME_CONFIG.EMERGENCY_LOAN_RATE);
            if (interestPreview) {
                interestPreview.textContent = GameUtils.formatCurrency(monthlyInterest);
            }
            
            // 확인 버튼 활성화/비활성화 (새로 생성된 버튼 사용)
            if (currentLoanAmount >= 1000) {
                newConfirmBtn.disabled = false;
                newConfirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                console.log('대출 신청 버튼 활성화:', currentLoanAmount);
            } else {
                newConfirmBtn.disabled = true;
                newConfirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                console.log('대출 신청 버튼 비활성화:', currentLoanAmount);
            }
        };
        
        // 초기 상태 설정
        currentLoanAmount = 0;
        updateDisplay();
        
        // 대출 금액 추가 버튼들
        const loanAmountButtons = modal.querySelectorAll('.loan-amount-btn');
        loanAmountButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const amount = parseInt(btn.dataset.amount);
                currentLoanAmount += amount;
                updateDisplay();
            });
        });
        
        // 초기화 버튼
        const resetBtn = document.getElementById('reset-loan-amount');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                currentLoanAmount = 0;
                updateDisplay();
            });
        }
        
        // $1,000 빼기 버튼
        const subtractBtn = document.getElementById('subtract-1000');
        if (subtractBtn) {
            subtractBtn.addEventListener('click', () => {
                if (currentLoanAmount >= 1000) {
                    currentLoanAmount -= 1000;
                    updateDisplay();
                }
            });
        }
        
        // 모달 표시
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        
        // 내부 콘텐츠 애니메이션 활성화
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.remove('opacity-0', 'scale-95');
            modalContent.classList.add('opacity-100', 'scale-100');
        }
        
        // 확인 버튼 클릭 이벤트 추가
        newConfirmBtn.addEventListener('click', () => {
            console.log('대출 신청 버튼 클릭, 현재 금액:', currentLoanAmount);
            if (currentLoanAmount >= 1000) {
                this.processEmergencyLoan(currentLoanAmount);
                this.hideEmergencyLoanModal();
            } else {
                console.log('대출 금액이 최소 금액 미만입니다.');
            }
        });
        
        // 취소 버튼 이벤트 (기존 이벤트 제거 후 새로 추가)
        const newCancelBtn = cancelBtn.cloneNode(true);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        
        newCancelBtn.addEventListener('click', () => {
            this.hideEmergencyLoanModal();
        });
    },

    // 긴급 대출 모달 숨기기
    hideEmergencyLoanModal() {
        const modal = document.getElementById('emergency-loan-modal');
        if (!modal) return;
        
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.remove('opacity-100', 'scale-100');
            modalContent.classList.add('opacity-0', 'scale-95');
        }
        
        // 애니메이션 후 숨기기
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }, 150);
    },

    // 긴급 대출 처리 (script.js.backup 기반)
    processEmergencyLoan(loanAmount) {
        try {
            const player = this.gameState.player;
            if (!player) return;

            // 대출 금액 유효성 검사
            if (loanAmount < 1000 || loanAmount > 1000000 || loanAmount % 1000 !== 0) {
                this.showModalNotification("오류", "대출 금액이 유효하지 않습니다.\n\n조건:\n- 최소: $1,000\n- 최대: $1,000,000\n- 단위: $1,000");
                return;
            }

            // 월 이자 계산
            const monthlyPayment = Math.round(loanAmount * GAME_CONFIG.EMERGENCY_LOAN_RATE);

            // 긴급 대출 객체 생성
            const emergencyLoan = {
                id: GameUtils.generateUUID(),
                amount: loanAmount,
                remainingAmount: loanAmount,
                monthlyPayment: monthlyPayment,
                type: 'emergency',
                isEmergencyLoan: true,
                createdAt: new Date().toISOString()
            };

            // 플레이어 상태 업데이트
            if (!player.emergencyLoans) {
                player.emergencyLoans = [];
            }
            player.emergencyLoans.push(emergencyLoan);
            
            // 현금 추가 (숫자 타입 보장)
            player.cash = parseFloat(player.cash || 0) + loanAmount;
            
            // 월 지출에 이자 추가
            player.expenses.other += monthlyPayment;

            // 게임 로그 추가
            this.addGameLog(`긴급 대출: ${GameUtils.formatCurrency(loanAmount)} 대출 (월 이자: ${GameUtils.formatCurrency(monthlyPayment)})`, 'liability');

            // 재무 상태 재계산
            this.recalculatePlayerFinancials();

            // UI 업데이트
            this.updateUI();
            StorageManager.saveGameState(this.gameState);

            // 완료 메시지
            this.showModalNotification(
                "긴급 대출 완료",
                `${GameUtils.formatCurrency(loanAmount)}의 긴급 대출이 승인되었습니다.\n\n대출 조건:\n- 대출 금액: ${GameUtils.formatCurrency(loanAmount)}\n- 월 이자: ${GameUtils.formatCurrency(monthlyPayment)} (연 ${(GAME_CONFIG.EMERGENCY_LOAN_RATE * 12 * 100).toFixed(1)}%)\n- 상환 방식: 전액 일시 상환\n\n현재 현금: ${GameUtils.formatCurrency(player.cash)}`
            );

        } catch (error) {
            console.error("긴급 대출 처리 중 오류:", error);
            this.addGameLog("긴급 대출 처리 중 오류가 발생했습니다.", 'error');
            this.showModalNotification("오류", "긴급 대출 처리 중 오류가 발생했습니다.");
        }
    },

    // 카드 모달 표시
    // showCardModal은 game-ui.js에서 처리 (중복 제거)

    // processCardAction은 game-ui.js에서 처리 (중복 제거)

    // 딜 카드 처리
    processDealCard(card) {
        const player = this.gameState.player;
        
        console.log('=== processDealCard 시작 ===');
        console.log('카드 정보:', {
            headline: card.headline,
            title: card.title,
            card_type: card.card_type,
            isOpportunity: card.isOpportunity,
            hasAssetDetails: !!(card.assetDetails),
            cost: card.cost,
            down_payment: card.down_payment,
            cash_flow: card.cash_flow
        });
        
        // 부동산 카드 확인 (assetType이 RealEstate이거나 실제 downPayment 값이 있는 경우)
        const isRealEstate = (card.assetDetails && card.assetDetails.assetType === 'RealEstate') || 
                            (card.downPayment && card.downPayment > 0) || 
                            (card.down_payment && card.down_payment > 0);
        
        console.log('=== 카드 타입 확인 ===');
        console.log('카드 정보:', {
            title: card.title,
            assetType: card.assetDetails?.assetType,
            downPayment: card.downPayment,
            down_payment: card.down_payment,
            card_type: card.card_type,
            isOpportunity: card.isOpportunity
        });
        console.log('부동산 카드 여부:', isRealEstate);
        
        if (isRealEstate) {
            console.log('→ handlePropertyPurchase로 이동 (부동산 카드)');
            this.handlePropertyPurchase(card);
        } else if (card.isOpportunity) {
            // 투자 기회 카드
            const isStock = card.assetDetails && card.assetDetails.assetType === 'Stock';
            const isTitleStock = card.title && card.title.includes('주식');
            const finalIsStock = isStock || isTitleStock;
            
            const isFund = (card.assetDetails && card.assetDetails.assetType === 'Investment') || 
                           (card.title && card.title.includes('펀드'));
            
            if (finalIsStock || isFund) {
                console.log(`→ handleStockPurchase로 이동 (${finalIsStock ? '주식' : '펀드'} 감지:`, finalIsStock || isFund, ')');
                this.handleStockPurchase(card);
            } else {
                console.log('→ handleInvestmentPurchase로 이동');
                this.handleInvestmentPurchase(card);
            }
        } else {
            // 기타 거래 카드
            console.log('→ handleInvestmentPurchase로 이동 (기타)');
            this.handleInvestmentPurchase(card);
        }
    },

    // 주식/펀드 구매 처리
    handleStockPurchase(card) {
        const isFund = (card.assetDetails && card.assetDetails.assetType === 'Investment') || 
                       (card.title && card.title.includes('펀드'));
        const assetType = isFund ? '펀드' : '주식';
        
        console.log(`=== ${assetType} 구매 처리 시작 ===`);
        console.log('카드 정보:', card);
        
        const player = this.gameState.player;
        console.log('플레이어 현재 상태:', {
            현재현금: player.cash,
            기존stocks: player.stocks ? Object.keys(player.stocks) : '없음'
        });
        
        // 주식 심볼 추출 개선
        let stockSymbol = this.getStockSymbolFromTitle(card.title);
        console.log('1단계 심볼 추출 결과:', stockSymbol);
        
        if (!stockSymbol) {
            // 심볼 추출 실패시 대체 로직
            if (card.assetDetails && card.assetDetails.assetName) {
                // assetName에서 추출 시도
                const match = card.assetDetails.assetName.match(/([A-Z0-9]+)/);
                stockSymbol = match ? match[1] : 'UNKNOWN';
                console.log('2단계 assetName에서 추출:', stockSymbol);
            } else {
                // 타이틀에서 추출 시도 (더 관대한 패턴)
                const match = card.title.match(/([A-Z0-9]+)/);
                stockSymbol = match ? match[1] : 'STOCK';
                console.log('3단계 타이틀에서 추출:', stockSymbol);
            }
        }
        
        console.log('최종 주식 심볼:', stockSymbol);
        
        // 카드 데이터 기반으로 주당 가격과 배당금 결정
        let sharePrice, dividendPerShare;
        
        if (card.assetDetails && card.assetDetails.assetType === 'Stock') {
            // assetDetails가 있는 경우
            sharePrice = card.cost; // 보통 주당 가격
            dividendPerShare = card.assetDetails.dividendsPerShare || card.cashFlowChange || 0;
            console.log('주식 카드 (assetDetails 있음):', {
                주당가격: sharePrice,
                주당배당금: dividendPerShare,
                assetDetails: card.assetDetails
            });
        } else {
            // 일반적인 경우
            sharePrice = card.cost;
            dividendPerShare = card.cashFlowChange || 0;
            console.log('주식 카드 (일반):', {
                주당가격: sharePrice,
                주당배당금: dividendPerShare
            });
        }
        
        if (player.cash < sharePrice) {
            this.showModalNotification("알림", "현금이 부족합니다.");
            return;
        }

        // 미리 저장된 주식 수량 가져오기 (모달에서 버튼 클릭 시 저장됨)
        let shares = card._selectedShares || 1;
        
        console.log('✅ 사용할 주식 수량:', shares);
        console.log('✅ 카드에 저장된 수량:', card._selectedShares);
        
        // 만약 저장된 수량이 없다면 DOM에서 시도 (백업)
        if (!card._selectedShares) {
            console.log('⚠️ 저장된 수량 없음, DOM에서 확인 시도');
            const sharesInput = document.getElementById('stock-shares');
            if (sharesInput) {
                shares = parseInt(sharesInput.value) || 1;
                console.log('✅ DOM에서 가져온 수량:', shares);
            } else {
                console.log('⚠️ DOM에서도 찾을 수 없음, 기본값 1 사용');
                shares = 1;
            }
        }
        
        if (shares <= 0 || isNaN(shares)) {
            console.log('잘못된 수량 입력, 구매 취소:', shares);
            this.showModalNotification("알림", "유효한 수량을 입력하세요.");
            return;
        }

        const totalCost = sharePrice * shares;
        console.log('총 구매 비용:', totalCost);
        
        if (player.cash < totalCost) {
            console.log('현금 부족, 구매 취소');
            this.showModalNotification("알림", `현금이 부족합니다.\n필요 금액: ${GameUtils.formatCurrency(totalCost)}\n보유 현금: ${GameUtils.formatCurrency(player.cash)}`);
            return;
        }

        // 월 배당금 계산
        const totalMonthlyDividend = dividendPerShare * shares;
        console.log('총 월 배당금:', totalMonthlyDividend);

        // 주식 구매
        player.cash -= totalCost;
        console.log('현금 차감 후:', player.cash);

        // 투자상품 저장소 초기화
        if (isFund) {
            if (!player.funds) {
                player.funds = {};
                console.log('funds 객체 새로 생성');
            }
        } else {
            if (!player.stocks) {
                player.stocks = {};
                console.log('stocks 객체 새로 생성');
            }
        }
        
        const investmentStorage = isFund ? player.funds : player.stocks;

        console.log(`구매 전 ${assetType} 객체:`, JSON.parse(JSON.stringify(investmentStorage)));

        // 동일 종목이 있는지 확인하여 합치기
        if (!investmentStorage[stockSymbol]) {
            investmentStorage[stockSymbol] = {
                shares: 0,
                totalInvested: 0,
                averagePrice: 0,
                monthlyDividend: 0
            };
            console.log(`${stockSymbol} 새 종목 생성:`, investmentStorage[stockSymbol]);
        }

        const currentInvestment = investmentStorage[stockSymbol];
        console.log('구매 전 현재 종목 상태:', JSON.parse(JSON.stringify(currentInvestment)));
        
        // 기존 보유분과 합치기 (가중평균으로 평균단가 계산)
        currentInvestment.totalInvested += totalCost;
        currentInvestment.shares += shares;
        currentInvestment.averagePrice = currentInvestment.totalInvested / currentInvestment.shares;
        currentInvestment.monthlyDividend += totalMonthlyDividend;

        console.log('구매 후 현재 종목 상태:', JSON.parse(JSON.stringify(currentInvestment)));
        console.log(`구매 후 전체 ${assetType}:`, JSON.parse(JSON.stringify(investmentStorage)));
        

        // 재무 상태 재계산 (수동 소득은 여기서 자동 계산됨)
        this.recalculatePlayerFinancials();
        
        // 로그 추가
        const unit = isFund ? '좌' : '주';
        const unitPrice = isFund ? '좌당' : '주당';
        const incomeType = isFund ? '월 수익' : '월 배당금';
        
        this.addGameLog(`${stockSymbol} ${assetType} ${shares}${unit}를 ${unitPrice} ${GameUtils.formatCurrency(sharePrice)}에 구매했습니다.`);
        this.addGameLog(`총 보유: ${currentInvestment.shares}${unit}, 평균단가: ${GameUtils.formatCurrency(currentInvestment.averagePrice)}`);
        if (totalMonthlyDividend > 0) {
            this.addGameLog(`${incomeType}: ${GameUtils.formatCurrency(totalMonthlyDividend)} 증가`);
        }
        
        console.log('=== UI 업데이트 및 저장 ===');
        console.log('저장 전 gameState.player.stocks:', JSON.parse(JSON.stringify(this.gameState.player.stocks)));
        console.log('저장 전 gameState.player.funds:', JSON.parse(JSON.stringify(this.gameState.player.funds || {})));
        
        this.updateUI();
        StorageManager.saveGameState(this.gameState);
        
        // 사용된 선택 수량 정리
        delete card._selectedShares;
        
        // 저장 확인
        const savedState = StorageManager.loadGameState();
        console.log('저장 후 확인 - saved stocks:', savedState && savedState.player ? savedState.player.stocks : 'null');
        console.log('=== 주식 구매 처리 완료 ===');
        
        // 구매 완료 후 자산/부채 탭으로 이동
        setTimeout(() => {
            this.showTab('assets');
        }, 500);
    },

    // 투자 구매 처리
    handleInvestmentPurchase(card) {
        const player = this.gameState.player;
        
        console.log('=== handleInvestmentPurchase 시작 ===');
        console.log('카드 정보:', card);
        
        // 주식인 경우 handleStockPurchase로 리다이렉트
        if (card.assetDetails && card.assetDetails.assetType === 'Stock') {
            console.log('주식 카드를 handleStockPurchase로 리다이렉트');
            this.handleStockPurchase(card);
            return;
        }
        
        if (player.cash < card.cost) {
            this.showModalNotification("알림", "현금이 부족합니다.");
            return;
        }

        player.cash -= card.cost;

        // 자산 추가 - 고유 ID 생성
        const asset = {
            id: `asset_${card.title.replace(/[^a-zA-Z0-9가-힣]/g, '_')}_${card.assetDetails?.assetType || 'Investment'}_${Date.now()}`,
            name: card.title,
            type: card.assetDetails?.assetType || 'Investment',
            monthlyIncome: card.cashFlowChange || 0,
            totalValue: card.cost,
            downPayment: card.cost
        };

        player.assets.push(asset);
        player.passiveIncome += asset.monthlyIncome;
        
        this.recalculatePlayerFinancials();
        this.addGameLog(`${card.title}에 ${GameUtils.formatCurrency(card.cost)}를 투자했습니다.`);
        
        // UI 업데이트 및 저장
        this.updateUI();
        StorageManager.saveGameState(this.gameState);
        
        // 구매 완료 후 자산/부채 탭으로 이동
        setTimeout(() => {
            this.showTab('assets');
        }, 500);
    },

    // 부동산 구매 처리
    handlePropertyPurchase(card) {
        console.log('=== handlePropertyPurchase 함수 호출됨 ===');
        console.log('호출 스택:', new Error().stack);
        
        const player = this.gameState.player;
        if (!player) {
            console.error('❌ 플레이어 정보가 없습니다!');
            return;
        }
        
        // 부동산 카드 정보 추출
        const propertyName = card.headline || card.title;
        const totalCost = card.cost;
        const downPayment = card.downPayment || card.down_payment || 0;
        const monthlyIncome = card.cashFlowChange || card.cash_flow || 0;
        // debtIncurred가 null이면 모기지 없음 (현금 구매만 가능)
        const mortgageAmount = card.debtIncurred === null ? 0 : (card.debtIncurred || (totalCost - downPayment));
        
        console.log('=== 부동산 카드 처리 시작 ===');
        console.log('원본 카드 데이터:', {
            title: card.title,
            headline: card.headline,
            cost: card.cost,
            downPayment: card.downPayment,
            down_payment: card.down_payment,
            cashFlowChange: card.cashFlowChange,
            cash_flow: card.cash_flow,
            debtIncurred: card.debtIncurred
        });
        console.log('추출된 부동산 정보:', {
            name: propertyName,
            totalCost: totalCost,
            downPayment: downPayment,
            monthlyIncome: monthlyIncome,
            mortgageAmount: mortgageAmount,
            playerCash: player.cash
        });
        console.log('플레이어 정보:', {
            cash: player.cash,
            cashType: typeof player.cash,
            assets: player.assets?.length || 0,
            liabilities: player.liabilities?.length || 0
        });
        
        // 1. debtIncurred가 null인 경우 - 모기지 없음, 현금 구매만 가능
        if (card.debtIncurred === null) {
            console.log('→ debtIncurred가 null - 모기지 없음, 현금 구매만 가능');
            if (player.cash < totalCost) {
                console.log('→ 총 비용 지불 불가 - 카드 폐기');
                this.showModalNotification(
                    "구매 불가", 
                    `모기지를 이용할 수 없는 카드입니다. 총 비용을 현금으로 지불해야 합니다.\n\n필요 금액: ${GameUtils.formatCurrency(totalCost)}\n보유 현금: ${GameUtils.formatCurrency(player.cash)}\n\n카드가 폐기됩니다.`
                );
                this.addGameLog(`${propertyName} 구매 실패 - 모기지 없음, 총 비용 부족으로 카드 폐기`);
                return;
            }
            // 총 비용을 현금으로 지불하고 구매 완료 (모기지 없음)
            console.log('→ 총 비용 현금 지불하고 구매 진행 (모기지 없음)');
            this.completePurchase(player, propertyName, totalCost, totalCost, monthlyIncome, 0);
            return;
        }
        
        // 2. 계약금이 0인 경우 처리
        if (downPayment === 0) {
            // No Money Down Deal 여부 확인 (모기지가 총 비용과 같거나, 제목에 명시된 경우)
            const isNoMoneyDownDeal = (mortgageAmount >= totalCost) || 
                                     (card.title && (card.title.includes('No Money Down') || card.title.includes('계약금 없음'))) ||
                                     (card.description && (card.description.includes('No Money Down') || card.description.includes('계약금 없음')));
            
            if (isNoMoneyDownDeal) {
                console.log('→ No Money Down Deal - 즉시 구매 가능');
                this.completePurchase(player, propertyName, totalCost, downPayment, monthlyIncome, mortgageAmount);
                return;
            } else {
                // 계약금은 없지만 총 비용을 현금으로 지불해야 하는 경우
                console.log('→ 계약금 없는 일반 부동산 - 총 비용 현금 지불 필요');
                if (player.cash < totalCost) {
                    console.log('→ 총 비용 지불 불가 - 카드 폐기');
                    this.showModalNotification(
                        "구매 불가", 
                        `총 비용을 현금으로 지불할 수 없습니다.\n\n필요 금액: ${GameUtils.formatCurrency(totalCost)}\n보유 현금: ${GameUtils.formatCurrency(player.cash)}\n\n카드가 폐기됩니다.`
                    );
                    this.addGameLog(`${propertyName} 구매 실패 - 총 비용 부족으로 카드 폐기`);
                    return;
                }
                // 총 비용을 계약금으로 설정하여 현금에서 차감
                console.log('→ 총 비용만큼 현금 지불하고 구매 진행');
                this.completePurchase(player, propertyName, totalCost, totalCost, monthlyIncome, 0); // 모기지 없음
                return;
            }
        }
        
        // 3. 계약금이 있는 경우 - 계약금 보유액 확인
        console.log('=== 현금 확인 단계 ===');
        console.log('플레이어 현금 (typeof):', typeof player.cash, player.cash);
        console.log('필요 계약금 (typeof):', typeof downPayment, downPayment);
        console.log('현금 >= 계약금 ?', player.cash >= downPayment);
        console.log('현금 < 계약금 ?', player.cash < downPayment);
        
        if (player.cash < downPayment) {
            console.log('→ 계약금 부족 - 카드 폐기');
            console.log('상세 비교:', {
                playerCash: player.cash,
                downPayment: downPayment,
                difference: downPayment - player.cash,
                playerCashType: typeof player.cash,
                downPaymentType: typeof downPayment
            });
            this.showModalNotification(
                "구매 불가", 
                `계약금이 부족합니다.\n\n필요 계약금: ${GameUtils.formatCurrency(downPayment)}\n보유 현금: ${GameUtils.formatCurrency(player.cash)}\n\n카드가 폐기됩니다.`
            );
            this.addGameLog(`${propertyName} 구매 실패 - 계약금 부족으로 카드 폐기`);
            return;
        }
        
        // 4. 계약금 지불하고 구매 완료
        console.log('→ 계약금 지불하고 구매 진행');
        this.completePurchase(player, propertyName, totalCost, downPayment, monthlyIncome, mortgageAmount);
    },

    // 부동산 구매 완료 처리
    completePurchase(player, propertyName, totalCost, downPayment, monthlyIncome, mortgageAmount) {
        // 1. 계약금 지불 (0인 경우 지불하지 않음)
        if (downPayment > 0) {
            player.cash -= downPayment;
            console.log(`계약금 ${GameUtils.formatCurrency(downPayment)} 지불 완료`);
        }
        
        // 2. 자산 등록 
        const propertyId = `real_estate_${Date.now()}`;
        const asset = {
            id: propertyId,
            name: propertyName,
            type: 'RealEstate',
            assetType: 'RealEstate',
            monthlyIncome: monthlyIncome,          // 추가적인 호환성을 위해 유지
            monthlyCashFlow: monthlyIncome,        // FinancialCalculator에서 사용하는 필드명
            totalValue: totalCost,
            purchasePrice: totalCost,              // 원래 구매가격 저장 (판매 시 차액 계산용)
            downPayment: downPayment,
            purchaseDate: new Date().toISOString()
        };
        
        if (!player.assets) player.assets = [];
        player.assets.push(asset);
        console.log('자산 등록 완료:', asset);
        
        // 3. 월 현금흐름은 자산에 등록되어 recalculatePlayerFinancials에서 자동 계산됨
        console.log(`월 현금흐름 ${GameUtils.formatCurrency(monthlyIncome)} 증가 예정 (자산에 등록됨)`);
        console.log('자산 등록 전 passiveIncome:', player.passiveIncome);
        
        // 4. 모기지 부채 등록 (모기지 금액이 있는 경우)
        if (mortgageAmount > 0) {
            // 모기지 월 상환액 계산 (모기지 금액의 1.5% 기준)
            const monthlyMortgagePayment = Math.round(mortgageAmount * 0.015);
            
            const liability = {
                id: `mortgage_${Date.now()}`,
                name: `${propertyName} 모기지`,
                type: 'Mortgage',
                amount: mortgageAmount,
                remainingAmount: mortgageAmount, // 부채 잔액
                totalAmount: mortgageAmount,     // 총 부채 금액
                monthlyPayment: monthlyMortgagePayment, // 월 상환액
                interestRate: 1.5, // 연 18% (월 1.5%)
                isInitial: false,
                purchaseDate: new Date().toISOString(),
                propertyId: propertyId // 연결된 부동산 ID
            };
            
            if (!player.liabilities) player.liabilities = [];
            player.liabilities.push(liability);
            
            // 월 지출에 모기지 상환액 추가
            player.expenses.other = (player.expenses.other || 0) + monthlyMortgagePayment;
            
            console.log('모기지 부채 등록 완료:', liability);
            console.log(`월 모기지 상환액 ${GameUtils.formatCurrency(monthlyMortgagePayment)} 지출에 추가`);
        }
        
        // 5. 재정 상태 재계산
        console.log('재정 재계산 전 passiveIncome:', player.passiveIncome);
        this.recalculatePlayerFinancials();
        console.log('재정 재계산 후 passiveIncome:', player.passiveIncome);
        console.log('재정 재계산 후 totalIncome:', player.totalIncome);
        console.log('재정 재계산 후 monthlyCashFlow:', player.monthlyCashFlow);
        
        // 6. 게임 로그 및 UI 업데이트
        let logMessage;
        if (downPayment === totalCost) {
            // 총 비용을 현금으로 지불한 경우
            logMessage = `${propertyName}을(를) 현금 ${GameUtils.formatCurrency(downPayment)}로 구매했습니다.`;
        } else if (downPayment > 0) {
            // 계약금을 지불한 경우
            logMessage = `${propertyName}을(를) 계약금 ${GameUtils.formatCurrency(downPayment)}로 구매했습니다.`;
        } else {
            // No Money Down Deal
            logMessage = `${propertyName}을(를) 계약금 없이 구매했습니다 (No Money Down Deal).`;
        }
        
        this.addGameLog(logMessage);
        
        if (monthlyIncome > 0) {
            this.addGameLog(`월 현금흐름이 ${GameUtils.formatCurrency(monthlyIncome)} 증가했습니다.`);
        }
        
        if (mortgageAmount > 0) {
            const monthlyMortgagePayment = Math.round(mortgageAmount * 0.015);
            this.addGameLog(`모기지 ${GameUtils.formatCurrency(mortgageAmount)}가 부채로 등록되었습니다.`);
            this.addGameLog(`월 모기지 상환액 ${GameUtils.formatCurrency(monthlyMortgagePayment)}가 지출에 추가되었습니다.`);
        }
        
        // 7. 저장 및 UI 업데이트
        StorageManager.saveGameState(this.gameState);
        this.updateUI();
        
        // 8. 완료 알림
        let completionMessage = `🏠 ${propertyName} 구매 완료!\n\n구매 가격: ${GameUtils.formatCurrency(totalCost)}\n`;
        
        if (downPayment === totalCost) {
            // 총 비용을 현금으로 지불한 경우
            completionMessage += `지불 금액: ${GameUtils.formatCurrency(downPayment)} (현금 전액 지불)\n`;
            completionMessage += `모기지: 없음\n`;
        } else if (downPayment > 0) {
            // 계약금을 지불한 경우
            completionMessage += `계약금: ${GameUtils.formatCurrency(downPayment)}\n`;
            if (mortgageAmount > 0) {
                const monthlyMortgagePayment = Math.round(mortgageAmount * 0.015);
                completionMessage += `모기지: ${GameUtils.formatCurrency(mortgageAmount)}\n`;
                completionMessage += `월 모기지 상환액: ${GameUtils.formatCurrency(monthlyMortgagePayment)}\n`;
            }
        } else {
            // No Money Down Deal
            completionMessage += `계약금: ${GameUtils.formatCurrency(downPayment)} (No Money Down Deal)\n`;
            if (mortgageAmount > 0) {
                const monthlyMortgagePayment = Math.round(mortgageAmount * 0.015);
                completionMessage += `모기지: ${GameUtils.formatCurrency(mortgageAmount)}\n`;
                completionMessage += `월 모기지 상환액: ${GameUtils.formatCurrency(monthlyMortgagePayment)}\n`;
            }
        }
        
        completionMessage += `월 임대수입: ${GameUtils.formatCurrency(monthlyIncome)}`;
        
        // 순 현금흐름 계산 및 표시
        if (mortgageAmount > 0) {
            const monthlyMortgagePayment = Math.round(mortgageAmount * 0.015);
            const netCashFlow = monthlyIncome - monthlyMortgagePayment;
            completionMessage += `\n순 월 현금흐름: ${GameUtils.formatCurrency(netCashFlow)} (임대수입 - 모기지 상환액)`;
        }
            
        this.showModalNotification("구매 완료", completionMessage);
        
        // 구매 완료 후 자산/부채 탭으로 이동
        setTimeout(() => {
            this.showTab('assets');
        }, 1000); // 알림 모달이 표시된 후 이동
    },


    // 자산 판매 모달 표시 (주식 및 일반 자산 지원)
    showSellAssetModal(assetId, assetType) {
        const player = this.gameState.player;
        let asset = null;
        let isFund = false;
        
        // 자산 유형에 따라 자산 정보 가져오기
        if (assetType === 'Stock') {
            const stockSymbol = assetId.replace('stock_', '');
            const stockData = player.stocks[stockSymbol];
            if (stockData) {
                asset = {
                    id: assetId,
                    name: `${stockSymbol} 주식`,
                    type: 'Stock',
                    shares: stockData.shares,
                    totalValue: stockData.shares * stockData.averagePrice,
                    monthlyIncome: stockData.monthlyDividend || 0,
                    averagePrice: stockData.averagePrice,
                    totalInvested: stockData.totalInvested,
                    symbol: stockSymbol
                };
            }
        } else if (assetType === 'Fund') {
            const fundSymbol = assetId.replace('fund_', '');
            const fundData = player.funds[fundSymbol];
            if (fundData) {
                asset = {
                    id: assetId,
                    name: `${fundSymbol} 펀드`,
                    type: 'Fund',
                    shares: fundData.shares,
                    totalValue: fundData.shares * fundData.averagePrice,
                    monthlyIncome: fundData.monthlyDividend || 0,
                    averagePrice: fundData.averagePrice,
                    totalInvested: fundData.totalInvested,
                    symbol: fundSymbol
                };
                isFund = true;
            }
        } else {
            // 일반 자산은 getAllPlayerAssets에서 찾기 (동적 ID 문제 해결)
            const allAssets = this.getAllPlayerAssets(player);
            asset = allAssets.find(a => a.id === assetId);
        }
        
        if (!asset) {
            this.showModalNotification("오류", "자산을 찾을 수 없습니다.");
            return;
        }
        
        this.gameState.currentSellingAssetId = assetId;
        this.gameState.currentSellingAssetType = assetType;
        this.gameState.currentSellingAsset = asset;
        
        const modal = document.getElementById('sell-asset-modal');
        const assetDetailsEl = document.getElementById('sell-asset-modal-details');
        const stockFieldsEl = document.getElementById('stock-sell-fields');
        const generalFieldsEl = document.getElementById('general-sell-fields');
        const sellSharesEl = document.getElementById('sell-shares');
        const sellPricePerShareEl = document.getElementById('sell-price-per-share');
        const maxSharesInfoEl = document.getElementById('max-shares-info');
        const totalSellAmountEl = document.getElementById('total-sell-amount-value');
        const sellPriceEl = document.getElementById('sell-price');
        
        if (!modal || !assetDetailsEl) {
            console.error('자산 판매 모달 요소를 찾을 수 없습니다.');
            return;
        }
        
        // 자산 정보 표시
        let detailsHTML = `<p><strong>자산명:</strong> ${asset.name}</p>`;
        
        if (asset.type === 'Stock' || asset.type === 'Fund') {
            const unit = isFund ? '좌' : '주';
            const unitPrice = isFund ? '좌당' : '주당';
            const incomeType = isFund ? '월 수익' : '월 배당금';
            
            detailsHTML += `
                <p><strong>보유 수량:</strong> ${asset.shares}${unit}</p>
                <p><strong>평균 매입가:</strong> ${GameUtils.formatCurrency(asset.averagePrice)}</p>
                <p><strong>총 투자금액:</strong> ${GameUtils.formatCurrency(asset.totalInvested)}</p>
                <p><strong>현재 가치:</strong> ${GameUtils.formatCurrency(asset.totalValue)}</p>
                <p><strong>${incomeType}:</strong> ${GameUtils.formatCurrency(asset.monthlyIncome)}</p>
            `;
            
            // 주식/펀드 판매 필드 표시
            if (stockFieldsEl && generalFieldsEl) {
                stockFieldsEl.classList.remove('hidden');
                generalFieldsEl.classList.add('hidden');
                
                // 최대 수량 정보 표시
                if (maxSharesInfoEl) {
                    maxSharesInfoEl.textContent = `최대 판매 가능: ${asset.shares}${unit}`;
                }
                
                // 초기값 설정
                if (sellSharesEl) {
                    sellSharesEl.value = asset.shares;
                    sellSharesEl.max = asset.shares;
                }
                if (sellPricePerShareEl) {
                    sellPricePerShareEl.value = asset.averagePrice;
                }
                
                // 총 판매 금액 계산 함수
                const updateTotalAmount = () => {
                    const shares = parseInt(sellSharesEl.value) || 0;
                    const pricePerShare = parseFloat(sellPricePerShareEl.value) || 0;
                    const totalAmount = shares * pricePerShare;
                    
                    if (totalSellAmountEl) {
                        totalSellAmountEl.textContent = GameUtils.formatCurrency(totalAmount);
                    }
                };
                
                // 실시간 계산 이벤트 리스너 추가
                if (sellSharesEl && sellPricePerShareEl) {
                    sellSharesEl.addEventListener('input', updateTotalAmount);
                    sellPricePerShareEl.addEventListener('input', updateTotalAmount);
                }
                
                // 초기 총액 계산
                updateTotalAmount();
            }
        } else if (asset.type === 'RealEstate' || asset.assetType === 'RealEstate') {
            // 부동산 자산 - 특별 정보 표시
            const purchasePrice = asset.purchasePrice || asset.totalValue || 0;
            const downPayment = asset.downPayment || 0;
            const monthlyIncome = asset.monthlyIncome || 0;
            
            detailsHTML += `
                <p><strong>자산 타입:</strong> 부동산 (RealEstate)</p>
                <p><strong>구매가격:</strong> ${GameUtils.formatCurrency(purchasePrice)}</p>
                <p><strong>월 임대수입:</strong> ${GameUtils.formatCurrency(monthlyIncome)}</p>
                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800"><strong>⚠️ 부동산 판매 안내</strong></p>
                    <p class="text-xs text-yellow-700">판매가격을 입력하면 (판매가 - 구매가) 차액만 현금으로 수령됩니다.</p>
                    <p class="text-xs text-yellow-700">연결된 모기지가 있다면 자동으로 청산됩니다.</p>
                </div>
            `;
            
            // 일반 자산 판매 필드 표시 (총 판매가액만)
            if (stockFieldsEl && generalFieldsEl && sellPriceEl) {
                stockFieldsEl.classList.add('hidden');
                generalFieldsEl.classList.remove('hidden');
                sellPriceEl.value = purchasePrice; // 구매가를 기본값으로 설정
                sellPriceEl.placeholder = "판매 희망가격 입력";
            }
        } else {
            // 일반 자산 (Collectible, Loan, Investment 등) - 수량 없이 총액만
            detailsHTML += `
                <p><strong>자산 타입:</strong> ${asset.type || 'Investment'}</p>
                <p><strong>현재 가치:</strong> ${GameUtils.formatCurrency(asset.totalValue || asset.currentValue || 0)}</p>
                <p><strong>월 현금흐름:</strong> ${GameUtils.formatCurrency(asset.monthlyIncome || 0)}</p>
            `;
            
            // 일반 자산 판매 필드 표시 (총 판매가액만)
            if (stockFieldsEl && generalFieldsEl && sellPriceEl) {
                stockFieldsEl.classList.add('hidden');
                generalFieldsEl.classList.remove('hidden');
                sellPriceEl.value = asset.totalValue || asset.currentValue || 0;
            }
        }
        
        assetDetailsEl.innerHTML = detailsHTML;
        
        // 모달 표시
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        
        // 내부 콘텐츠 애니메이션 활성화
        const modalContent = modal.querySelector('.bg-white');
        if (modalContent) {
            modalContent.classList.remove('opacity-0', 'scale-95');
            modalContent.classList.add('opacity-100', 'scale-100');
        }
    },

    // 자산 판매 모달 숨기기
    hideSellAssetModal() {
        const modal = document.getElementById('sell-asset-modal');
        if (modal) {
            this.hideModal(modal);
        }
        this.gameState.currentSellingAssetId = null;
        this.gameState.currentSellingAssetType = null;
        this.gameState.currentSellingAsset = null;
        
        // 이벤트 리스너 정리 (메모리 누수 방지)
        const sellSharesEl = document.getElementById('sell-shares');
        const sellPricePerShareEl = document.getElementById('sell-price-per-share');
        if (sellSharesEl && sellPricePerShareEl) {
            sellSharesEl.removeEventListener('input', this.updateTotalAmount);
            sellPricePerShareEl.removeEventListener('input', this.updateTotalAmount);
        }
    },

    // 자산 판매 처리
    processSellAsset(assetId, sellPrice, sellShares = null, sellPricePerShare = null) {
        const player = this.gameState.player;
        const assetType = this.gameState.currentSellingAssetType;
        
        if (assetType === 'Stock') {
            // 주식 판매 처리
            const stockSymbol = assetId.replace('stock_', '');
            const stockData = player.stocks[stockSymbol];
            
            if (!stockData) {
                this.showModalNotification("오류", "주식을 찾을 수 없습니다.");
                return;
            }
            
            // 부분 판매인지 전체 판매인지 확인
            if (sellShares && sellShares < stockData.shares) {
                // 부분 판매 처리
                const remainingShares = stockData.shares - sellShares;
                const soldDividend = (stockData.monthlyDividend / stockData.shares) * sellShares;
                const remainingDividend = stockData.monthlyDividend - soldDividend;
                const soldInvestment = (stockData.totalInvested / stockData.shares) * sellShares;
                const remainingInvestment = stockData.totalInvested - soldInvestment;
                
                // 현금 증가 (숫자 타입 보장)
                player.cash = parseFloat(player.cash || 0) + sellPrice;
                
                // 주식 데이터 업데이트 (남은 주식으로)
                stockData.shares = remainingShares;
                stockData.monthlyDividend = remainingDividend;
                stockData.totalInvested = remainingInvestment;
                // 평균 매입가는 동일하게 유지
                
                this.addGameLog(`${stockSymbol} 주식 ${sellShares}주를 주당 ${GameUtils.formatCurrency(sellPricePerShare)}에 판매했습니다. (총 ${GameUtils.formatCurrency(sellPrice)})`);
                this.addGameLog(`남은 보유량: ${remainingShares}주, 남은 월 배당금: ${GameUtils.formatCurrency(remainingDividend)}`);
            } else {
                // 전체 판매 처리
                player.cash += sellPrice;
                
                this.addGameLog(`${stockSymbol} 주식 ${stockData.shares}주를 ${GameUtils.formatCurrency(sellPrice)}에 전량 판매했습니다.`);
                
                // 주식 삭제
                delete player.stocks[stockSymbol];
            }
            
        } else if (assetType === 'Fund') {
            // 펀드 판매 처리
            const fundSymbol = assetId.replace('fund_', '');
            const fundData = player.funds[fundSymbol];
            
            if (!fundData) {
                this.showModalNotification("오류", "펀드를 찾을 수 없습니다.");
                return;
            }
            
            // 부분 판매인지 전체 판매인지 확인
            if (sellShares && sellShares < fundData.shares) {
                // 부분 판매 처리
                const remainingShares = fundData.shares - sellShares;
                const soldDividend = (fundData.monthlyDividend / fundData.shares) * sellShares;
                const remainingDividend = fundData.monthlyDividend - soldDividend;
                const soldInvestment = (fundData.totalInvested / fundData.shares) * sellShares;
                const remainingInvestment = fundData.totalInvested - soldInvestment;
                
                // 현금 증가 (숫자 타입 보장)
                player.cash = parseFloat(player.cash || 0) + sellPrice;
                
                // 펀드 데이터 업데이트 (남은 좌수로)
                fundData.shares = remainingShares;
                fundData.monthlyDividend = remainingDividend;
                fundData.totalInvested = remainingInvestment;
                // 평균 매입가는 동일하게 유지
                
                this.addGameLog(`${fundSymbol} 펀드 ${sellShares}좌를 좌당 ${GameUtils.formatCurrency(sellPricePerShare)}에 판매했습니다. (총 ${GameUtils.formatCurrency(sellPrice)})`);
                this.addGameLog(`남은 보유량: ${remainingShares}좌, 남은 월 수익: ${GameUtils.formatCurrency(remainingDividend)}`);
            } else {
                // 전체 판매 처리
                player.cash += sellPrice;
                
                this.addGameLog(`${fundSymbol} 펀드 ${fundData.shares}좌를 ${GameUtils.formatCurrency(sellPrice)}에 전량 판매했습니다.`);
                
                // 펀드 삭제
                delete player.funds[fundSymbol];
            }
            
        } else {
            // 일반 자산 판매 처리 - getAllPlayerAssets에서 자산 정보 확인
            const allAssets = this.getAllPlayerAssets(player);
            const assetInfo = allAssets.find(a => a.id === assetId);
            
            if (!assetInfo) {
                this.showModalNotification("오류", "자산을 찾을 수 없습니다.");
                return;
            }

            // originalIndex가 있으면 직접 사용, 없으면 이름으로 찾기
            let assetIndex = -1;
            let originalAsset = null;
            
            if (typeof assetInfo.originalIndex === 'number' && assetInfo.originalIndex < player.assets.length) {
                // originalIndex 사용
                assetIndex = assetInfo.originalIndex;
                originalAsset = player.assets[assetIndex];
                
                // 추가 검증 (인덱스가 올바른 자산을 가리키는지 확인)
                if (originalAsset.name !== assetInfo.name) {
                    // 인덱스가 잘못된 경우 이름으로 찾기
                    assetIndex = -1;
                    originalAsset = null;
                }
            }
            
            if (assetIndex === -1) {
                // 이름과 타입으로 원본 자산 찾기 (백업 방법)
                for (let i = 0; i < player.assets.length; i++) {
                    const asset = player.assets[i];
                    if (asset.name === assetInfo.name && 
                        (asset.type === assetInfo.type || (!asset.type && assetInfo.type === 'Investment'))) {
                        assetIndex = i;
                        originalAsset = asset;
                        break;
                    }
                }
            }
            
            if (assetIndex === -1 || !originalAsset) {
                this.showModalNotification("오류", "원본 자산을 찾을 수 없습니다.");
                return;
            }

            // 부동산 판매 특별 처리
            if (originalAsset.type === 'RealEstate' || originalAsset.assetType === 'RealEstate') {
                console.log('=== 부동산 판매 처리 시작 ===');
                console.log('판매할 부동산:', originalAsset);
                
                const purchasePrice = originalAsset.purchasePrice || originalAsset.totalValue || 0;
                const propertyId = originalAsset.id;
                
                // 연결된 모기지 찾기
                let mortgageToRemove = null;
                let mortgageIndex = -1;
                
                if (player.liabilities && player.liabilities.length > 0) {
                    for (let i = 0; i < player.liabilities.length; i++) {
                        const liability = player.liabilities[i];
                        if (liability.type === 'Mortgage' && liability.propertyId === propertyId) {
                            mortgageToRemove = liability;
                            mortgageIndex = i;
                            break;
                        }
                    }
                }
                
                console.log('연결된 모기지:', mortgageToRemove);
                
                // 모기지가 있는 경우 청산 처리
                let cashReceived = sellPrice; // 판매가 전체를 먼저 받음
                
                if (mortgageToRemove) {
                    // 판매가에서 모기지 잔액 상환
                    cashReceived = sellPrice - mortgageToRemove.amount;
                    
                    // 판매가 < 모기지 잔액인 경우 현금 부족 확인
                    if (cashReceived < 0) {
                        const requiredCash = Math.abs(cashReceived);
                        if (player.cash < requiredCash) {
                            // 현금이 부족하면 판매 거부
                            this.showModalNotification(
                                "판매 불가", 
                                `모기지 상환을 위해 추가 현금이 필요합니다.\n\n판매가: ${GameUtils.formatCurrency(sellPrice)}\n모기지 잔액: ${GameUtils.formatCurrency(mortgageToRemove.amount)}\n필요 추가 현금: ${GameUtils.formatCurrency(requiredCash)}\n보유 현금: ${GameUtils.formatCurrency(player.cash)}\n\n판매를 취소합니다.`
                            );
                            console.log(`부동산 판매 취소 - 모기지 상환을 위한 현금 부족`);
                            return;
                        }
                    }
                    
                    // 월 지출에서 모기지 상환액 제거
                    player.expenses.other = Math.max(0, (player.expenses.other || 0) - mortgageToRemove.monthlyPayment);
                    
                    // 모기지 부채 제거
                    player.liabilities.splice(mortgageIndex, 1);
                    
                    console.log(`모기지 청산: ${GameUtils.formatCurrency(mortgageToRemove.amount)}`);
                    console.log(`월 상환액 ${GameUtils.formatCurrency(mortgageToRemove.monthlyPayment)} 지출에서 제거`);
                    
                    this.addGameLog(`${assetInfo.name} 판매 완료`);
                    this.addGameLog(`판매가 ${GameUtils.formatCurrency(sellPrice)}에서 모기지 ${GameUtils.formatCurrency(mortgageToRemove.amount)} 상환`);
                    this.addGameLog(`월 모기지 상환액 ${GameUtils.formatCurrency(mortgageToRemove.monthlyPayment)} 지출에서 제거`);
                    
                    // 모기지 상환 후 현금 수령 결과
                    if (cashReceived > 0) {
                        this.addGameLog(`모기지 상환 후 현금 ${GameUtils.formatCurrency(cashReceived)} 수령`);
                    } else if (cashReceived < 0) {
                        this.addGameLog(`모기지 상환을 위해 추가로 ${GameUtils.formatCurrency(Math.abs(cashReceived))} 현금 지불`);
                    } else {
                        this.addGameLog(`모기지 상환으로 판매가 모두 소진`);
                    }
                } else {
                    // 모기지가 없는 경우 - 판매가 전체를 현금으로 수령
                    this.addGameLog(`${assetInfo.name} 판매: ${GameUtils.formatCurrency(sellPrice)} 현금 수령`);
                }
                
                // 현금 증가 (모기지 상환 후 남은 금액, 숫자 타입 보장)
                player.cash = parseFloat(player.cash || 0) + cashReceived;
                
            } else {
                // 일반 자산 판매 (숫자 타입 보장)
                player.cash = parseFloat(player.cash || 0) + sellPrice;
                this.addGameLog(`${assetInfo.name}을(를) ${GameUtils.formatCurrency(sellPrice)}에 판매했습니다.`);
            }
            
            // 자산 목록에서 제거
            player.assets.splice(assetIndex, 1);
        }
        
        this.recalculatePlayerFinancials();
        this.updateUI();
        StorageManager.saveGameState(this.gameState);
    },

    // 모달 숨기기 헬퍼 함수
    hideModal(modal) {
        const modalContent = modal.querySelector('.bg-white') || modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.remove('opacity-100', 'scale-100');
            modalContent.classList.add('opacity-0', 'scale-95');
        }
        
        // 애니메이션 후 숨기기
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }, 150);
    },

    // 카드목록 페이지로 이동하며 특정 카드 타입 선택
    goToCardListPage(cardType) {
        console.log(`카드목록 페이지로 이동: ${cardType}`);
        
        // 카드 타입 정규화
        const normalizedCardType = cardType;
        
        // 해당 카드 타입의 카드가 있는지 확인
        const availableCards = CARD_DATA[normalizedCardType];
        if (!availableCards || availableCards.length === 0) {
            this.addGameLog(`${normalizedCardType} 카드가 없습니다.`, 'error');
            return;
        }
        
        // 카드 목록 탭으로 이동
        this.switchTab('cards');
        
        // 카드 타입 선택 드롭다운 업데이트
        const cardTypeSelect = document.getElementById('card-type-select');
        if (cardTypeSelect) {
            cardTypeSelect.value = normalizedCardType;
        }
        
        // 카드 목록 렌더링
        this.renderCardList(normalizedCardType);
    },

    // 카드 액션 처리
    handleCardAction(card) {
        const player = this.gameState.player;
        if (!player) return;

        // 카드 타입에 따라 다른 처리
        if (card.cardType === 'Doodads' || (!card.cardType && card.cost && !card.assetDetails)) {
            this.processDoodadCard(card);
        } else if (card.assetDetails) {
            this.processDealCard(card);
        } else {
            this.addGameLog(`${card.title} 카드를 처리했습니다.`);
        }

        this.updateUI();
        StorageManager.saveGameState(this.gameState);
    },

    // 꿈 비용 USD를 KRW로 변환하여 표시
    async updateDreamCostKrw(usdAmount) {
        const krwDisplay = document.getElementById('dream-cost-krw');
        const krwValue = document.getElementById('dream-cost-krw-value');
        
        if (!krwDisplay || !krwValue) return;
        
        if (!usdAmount || usdAmount === 0 || isNaN(usdAmount)) {
            krwDisplay.classList.add('hidden');
            return;
        }
        
        try {
            // 환율 변환
            const krwAmount = await GameUtils.convertUsdToKrw(usdAmount);
            const formattedKrw = GameUtils.formatKrw(krwAmount);
            
            krwValue.textContent = formattedKrw;
            krwDisplay.classList.remove('hidden');
        } catch (error) {
            console.error('KRW 변환 중 오류:', error);
            krwDisplay.classList.add('hidden');
        }
    }


});

// DOM 로드 완료 시 게임 인스턴스 생성 (자동 실행 방지 플래그 확인)
document.addEventListener('DOMContentLoaded', () => {
    // 자동 실행 방지 플래그가 설정되어 있으면 게임을 자동으로 시작하지 않음
    if (!window.DISABLE_AUTO_GAME_INIT) {
        window.cashflowGame = new CashflowGame();
    }
});