// 직업별 초기 재무 데이터
const PROFESSION_DATA = [
    {
        "jobTitle": "청소부",
        "originalTitle": "Janitor",
        "incomeStatement": {
            "salary": 1600,
            "passiveIncome": 0,
            "totalIncome": 1600
        },
        "expenses": {
            "taxes": 280,
            "homeMortgagePayment": 200,
            "schoolLoanPayment": 0,
            "carLoanPayment": 60,
            "creditCardPayment": 60,
            "retailExpenses": 50,
            "otherExpenses": 300,
            "childExpensesPerChild": 70,
            "numberOfChildren": 0,
            "totalExpenses": 950
        },
        "balanceSheet": {
            "assets": {
                "cash": 560
            },
            "liabilities": {
                "homeMortgage": 20000,
                "schoolLoans": 0,
                "carLoans": 4000,
                "creditCardDebt": 2000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 650,
            "payDayAmount": 650
        }
    },
    {
        "jobTitle": "정비공",
        "originalTitle": "Mechanic",
        "incomeStatement": {
            "salary": 2000,
            "passiveIncome": 0,
            "totalIncome": 2000
        },
        "expenses": {
            "taxes": 360,
            "homeMortgagePayment": 300,
            "schoolLoanPayment": 0,
            "carLoanPayment": 60,
            "creditCardPayment": 60,
            "retailExpenses": 50,
            "otherExpenses": 450,
            "childExpensesPerChild": 110,
            "numberOfChildren": 0,
            "totalExpenses": 1280
        },
        "balanceSheet": {
            "assets": {
                "cash": 670
            },
            "liabilities": {
                "homeMortgage": 31000,
                "schoolLoans": 0,
                "carLoans": 3000,
                "creditCardDebt": 2000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 720,
            "payDayAmount": 720
        }
    },
    {
        "jobTitle": "비서",
        "originalTitle": "Secretary",
        "incomeStatement": {
            "salary": 2500,
            "passiveIncome": 0,
            "totalIncome": 2500
        },
        "expenses": {
            "taxes": 460,
            "homeMortgagePayment": 400,
            "schoolLoanPayment": 0,
            "carLoanPayment": 80,
            "creditCardPayment": 60,
            "retailExpenses": 50,
            "otherExpenses": 570,
            "childExpensesPerChild": 140,
            "numberOfChildren": 0,
            "totalExpenses": 1620
        },
        "balanceSheet": {
            "assets": {
                "cash": 710
            },
            "liabilities": {
                "homeMortgage": 38000,
                "schoolLoans": 0,
                "carLoans": 4000,
                "creditCardDebt": 2000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 880,
            "payDayAmount": 880
        }
    },
    {
        "jobTitle": "트럭 운전사",
        "originalTitle": "Truck Driver",
        "incomeStatement": {
            "salary": 2500,
            "passiveIncome": 0,
            "totalIncome": 2500
        },
        "expenses": {
            "taxes": 460,
            "homeMortgagePayment": 400,
            "schoolLoanPayment": 0,
            "carLoanPayment": 80,
            "creditCardPayment": 60,
            "retailExpenses": 50,
            "otherExpenses": 570,
            "childExpensesPerChild": 140,
            "numberOfChildren": 0,
            "totalExpenses": 1620
        },
        "balanceSheet": {
            "assets": {
                "cash": 750
            },
            "liabilities": {
                "homeMortgage": 38000,
                "schoolLoans": 0,
                "carLoans": 4000,
                "creditCardDebt": 2000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 880,
            "payDayAmount": 880
        }
    },
    {
        "jobTitle": "경찰관",
        "originalTitle": "Police Officer",
        "incomeStatement": {
            "salary": 3000,
            "passiveIncome": 0,
            "totalIncome": 3000
        },
        "expenses": {
            "taxes": 580,
            "homeMortgagePayment": 400,
            "schoolLoanPayment": 0,
            "carLoanPayment": 100,
            "creditCardPayment": 60,
            "retailExpenses": 50,
            "otherExpenses": 690,
            "childExpensesPerChild": 160,
            "numberOfChildren": 0,
            "totalExpenses": 1880
        },
        "balanceSheet": {
            "assets": {
                "cash": 520
            },
            "liabilities": {
                "homeMortgage": 46000,
                "schoolLoans": 0,
                "carLoans": 5000,
                "creditCardDebt": 2000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 1120,
            "payDayAmount": 1120
        }
    },
    {
        "jobTitle": "간호사",
        "originalTitle": "Nurse",
        "incomeStatement": {
            "salary": 3100,
            "passiveIncome": 0,
            "totalIncome": 3100
        },
        "expenses": {
            "taxes": 600,
            "homeMortgagePayment": 400,
            "schoolLoanPayment": 30,
            "carLoanPayment": 100,
            "creditCardPayment": 90,
            "retailExpenses": 50,
            "otherExpenses": 710,
            "childExpensesPerChild": 170,
            "numberOfChildren": 0,
            "totalExpenses": 1980
        },
        "balanceSheet": {
            "assets": {
                "cash": 480
            },
            "liabilities": {
                "homeMortgage": 47000,
                "schoolLoans": 6000,
                "carLoans": 5000,
                "creditCardDebt": 3000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 1120,
            "payDayAmount": 1120
        }
    },
    {
        "jobTitle": "교사",
        "originalTitle": "Teacher",
        "incomeStatement": {
            "salary": 3300,
            "passiveIncome": 0,
            "totalIncome": 3300
        },
        "expenses": {
            "taxes": 630,
            "homeMortgagePayment": 500,
            "schoolLoanPayment": 60,
            "carLoanPayment": 100,
            "creditCardPayment": 90,
            "retailExpenses": 50,
            "otherExpenses": 760,
            "childExpensesPerChild": 180,
            "numberOfChildren": 0,
            "totalExpenses": 2190
        },
        "balanceSheet": {
            "assets": {
                "cash": 400
            },
            "liabilities": {
                "homeMortgage": 50000,
                "schoolLoans": 12000,
                "carLoans": 5000,
                "creditCardDebt": 3000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 1110,
            "payDayAmount": 1110
        }
    },
    {
        "jobTitle": "비즈니스 매니저",
        "originalTitle": "Business Manager",
        "incomeStatement": {
            "salary": 4600,
            "passiveIncome": 0,
            "totalIncome": 4600
        },
        "expenses": {
            "taxes": 910,
            "homeMortgagePayment": 700,
            "schoolLoanPayment": 60,
            "carLoanPayment": 120,
            "creditCardPayment": 90,
            "retailExpenses": 50,
            "otherExpenses": 1000,
            "childExpensesPerChild": 240,
            "numberOfChildren": 0,
            "totalExpenses": 2930
        },
        "balanceSheet": {
            "assets": {
                "cash": 400
            },
            "liabilities": {
                "homeMortgage": 75000,
                "schoolLoans": 12000,
                "carLoans": 6000,
                "creditCardDebt": 3000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 1670,
            "payDayAmount": 1670
        }
    },
    {
        "jobTitle": "엔지니어",
        "originalTitle": "Engineer",
        "incomeStatement": {
            "salary": 4900,
            "passiveIncome": 0,
            "totalIncome": 4900
        },
        "expenses": {
            "taxes": 1050,
            "homeMortgagePayment": 700,
            "schoolLoanPayment": 60,
            "carLoanPayment": 140,
            "creditCardPayment": 120,
            "retailExpenses": 50,
            "otherExpenses": 1090,
            "childExpensesPerChild": 250,
            "numberOfChildren": 0,
            "totalExpenses": 3210
        },
        "balanceSheet": {
            "assets": {
                "cash": 400
            },
            "liabilities": {
                "homeMortgage": 75000,
                "schoolLoans": 12000,
                "carLoans": 7000,
                "creditCardDebt": 4000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 1690,
            "payDayAmount": 1690
        }
    },
    {
        "jobTitle": "변호사",
        "originalTitle": "Lawyer",
        "incomeStatement": {
            "salary": 7500,
            "passiveIncome": 0,
            "totalIncome": 7500
        },
        "expenses": {
            "taxes": 1830,
            "homeMortgagePayment": 1100,
            "schoolLoanPayment": 390,
            "carLoanPayment": 220,
            "creditCardPayment": 180,
            "retailExpenses": 50,
            "otherExpenses": 1650,
            "childExpensesPerChild": 380,
            "numberOfChildren": 0,
            "totalExpenses": 5420
        },
        "balanceSheet": {
            "assets": {
                "cash": 400
            },
            "liabilities": {
                "homeMortgage": 115000,
                "schoolLoans": 78000,
                "carLoans": 11000,
                "creditCardDebt": 6000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 2080,
            "payDayAmount": 2080
        }
    },
    {
        "jobTitle": "항공기 조종사",
        "originalTitle": "Airline Pilot",
        "incomeStatement": {
            "salary": 9500,
            "passiveIncome": 0,
            "totalIncome": 9500
        },
        "expenses": {
            "taxes": 2350,
            "homeMortgagePayment": 1330,
            "schoolLoanPayment": 0,
            "carLoanPayment": 300,
            "creditCardPayment": 660,
            "retailExpenses": 50,
            "otherExpenses": 2210,
            "childExpensesPerChild": 480,
            "numberOfChildren": 0,
            "totalExpenses": 6900
        },
        "balanceSheet": {
            "assets": {
                "cash": 400
            },
            "liabilities": {
                "homeMortgage": 143000,
                "schoolLoans": 0,
                "carLoans": 15000,
                "creditCardDebt": 22000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 2600,
            "payDayAmount": 2600
        }
    },
    {
        "jobTitle": "의사",
        "originalTitle": "Doctor",
        "incomeStatement": {
            "salary": 13200,
            "passiveIncome": 0,
            "totalIncome": 13200
        },
        "expenses": {
            "taxes": 3420,
            "homeMortgagePayment": 1900,
            "schoolLoanPayment": 750,
            "carLoanPayment": 380,
            "creditCardPayment": 270,
            "retailExpenses": 50,
            "otherExpenses": 2880,
            "childExpensesPerChild": 640,
            "numberOfChildren": 0,
            "totalExpenses": 9650
        },
        "balanceSheet": {
            "assets": {
                "cash": 400
            },
            "liabilities": {
                "homeMortgage": 202000,
                "schoolLoans": 150000,
                "carLoans": 19000,
                "creditCardDebt": 9000,
                "retailDebt": 1000
            }
        },
        "financialSummary": {
            "monthlyCashFlow": 3550,
            "payDayAmount": 3550
        }
    }
];


