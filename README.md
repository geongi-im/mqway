# MQWAY

MQ Way로 키우는 경제 지능과 자기주도력 통합 교육 플랫폼

## 프로젝트 소개

MQWAY는 아동과 부모를 위한 경제 교육 플랫폼입니다. 돈을 통해 삶의 지혜를 배우는 독창적인 MQ Way 방식으로, 아이가 스스로 미래를 설계하는 힘을 길러줍니다.

## 기술 스택

- **Backend**: Laravel 6.x (PHP 7.2+)
- **Frontend**: Blade Templates, JavaScript
- **Database**: MySQL 5.7
- **Infrastructure**: Docker (Nginx, PHP-FPM)

## 폴더 구조

```
mqway/
├── config/                       # 애플리케이션 설정 파일
├── data/                         # 데이터 저장소
│   └── mysql/                    # MySQL 데이터 디렉토리
│
├── html/                         # Laravel 애플리케이션 루트
│   ├── app/                      # 애플리케이션 코어
│   │   ├── Console/              # Artisan 콘솔 명령어
│   │   ├── Exceptions/           # 예외 처리
│   │   ├── Http/                 # HTTP 레이어
│   │   │   ├── Controllers/      # 컨트롤러
│   │   │   │   ├── Api/          # API 컨트롤러
│   │   │   │   └── Auth/         # 인증 관련 컨트롤러
│   │   │   └── Middleware/       # 미들웨어
│   │   ├── Logging/              # 로깅 설정
│   │   ├── Models/               # Eloquent 모델
│   │   ├── Providers/            # 서비스 프로바이더
│   │   ├── Rules/                # 커스텀 유효성 검사 규칙
│   │   ├── Services/             # 외부 연동 / 도메인 서비스 (뉴스 본문 추출, AI 분석)
│   │   ├── Traits/               # 공통 트레이트
│   │   └── View/                 # 뷰 컴포저
│   │
│   ├── bootstrap/                # 프레임워크 부트스트랩
│   │   └── cache/                # 프레임워크 캐시
│   │
│   ├── config/                   # Laravel 설정 파일
│   │
│   ├── database/                 # 데이터베이스 관련
│   │   ├── factories/            # 모델 팩토리
│   │   ├── migrations/           # 마이그레이션 파일
│   │   ├── seeders/              # 시더 (Laravel 8+)
│   │   └── seeds/                # 시더 (Laravel 6)
│   │
│   ├── public/                   # 웹 루트 디렉토리
│   │   ├── images/               # 이미지 파일
│   │   └── js/                   # 자바스크립트 파일
│   │
│   ├── resources/                # 뷰 및 에셋
│   │   ├── css/                  # CSS 파일
│   │   ├── js/                   # JavaScript 소스
│   │   ├── lang/                 # 다국어 파일
│   │   ├── sass/                 # SASS 파일
│   │   └── views/                # Blade 템플릿
│   │       ├── auth/             # 인증 뷰
│   │       ├── beecube/          # 비큐브
│   │       ├── board/            # 자유 게시판
│   │       ├── board_cartoon/    # 만화 게시판
│   │       ├── board_content/    # 콘텐츠 게시판
│   │       ├── board_insights/   # 인사이트 게시판
│   │       ├── board_mission/    # 미션 게시판
│   │       ├── board_portfolio/  # 포트폴리오 게시판
│   │       ├── board_research/   # 리서치 게시판
│   │       ├── board_scrap/      # 뉴스 스크랩 게시판 (AI 분석 폼 포함)
│   │       ├── board_video/      # 비디오 게시판
│   │       ├── cashflow/         # 캐시플로우 게임
│   │       ├── course/           # 코스 안내
│   │       ├── emails/           # 이메일 템플릿
│   │       ├── errors/           # 에러 페이지
│   │       ├── guidebook/        # 가이드북
│   │       ├── layouts/          # 레이아웃 템플릿
│   │       ├── mypage/           # 마이페이지
│   │       ├── news/             # 뉴스
│   │       ├── pick/             # 픽
│   │       └── tools/            # 교육 도구
│   │
│   ├── routes/                   # 라우팅 정의
│   │   ├── api.php               # API 라우트
│   │   └── web.php               # 웹 라우트
│   │
│   ├── storage/                  # 파일 저장소
│   │   ├── app/                  # 애플리케이션 파일
│   │   ├── framework/            # 프레임워크 파일
│   │   └── logs/                 # 로그 파일
│   │
│   ├── tests/                    # 테스트 코드
│   │   ├── Feature/              # 기능 테스트
│   │   └── Unit/                 # 단위 테스트
│   │
│   ├── vendor/                   # Composer 의존성
│   │
│   ├── composer.json             # Composer 설정
│   ├── package.json              # NPM 패키지 설정
│   └── webpack.mix.js            # Laravel Mix 설정
│
├── docker-compose.yml            # Docker Compose 설정
├── Dockerfile                    # PHP-FPM 컨테이너 설정
├── nginx.conf                    # Nginx 웹서버 설정
├── my.cnf                        # MySQL 설정
├── set_permissions.sh            # 권한 설정 스크립트
│
└── README.md                     # 프로젝트 문서
```

## 설치 및 실행

### 1. 환경 변수 설정

루트 디렉토리와 html 디렉토리에 각각 `.env` 파일을 생성합니다.

**루트 디렉토리 (.env)**
```bash
# Docker Compose 프로젝트 이름
COMPOSE_PROJECT_NAME=mqway

# 웹서버 설정 (docker-compose.yml이 ${WEB_PORT}로 참조하므로 필수)
# 호스트에서 80이 이미 점유되어 있으면 8080 등으로 바꾸고 APP_URL도 함께 맞출 것
WEB_PORT=80

# MySQL 설정
MYSQL_HOSTNAME=mysql
MYSQL_PORT=3306
MYSQL_ROOT_PASSWORD=your_root_password
MYSQL_DATABASE=mqway
MYSQL_USER=mqway_user
MYSQL_PASSWORD=your_password

# 사용자 UID/GID 설정
USER_ID=1000
GROUP_ID=1000
```

**html 디렉토리 (.env)**
```bash
APP_NAME=MQWAY
APP_ENV=local
APP_KEY=base64:your_app_key
APP_DEBUG=true
APP_URL=http://localhost

# 데이터베이스 설정
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mqway
DB_USERNAME=mqway_user
DB_PASSWORD=your_password

# Google OAuth 설정
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback

# Gemini (뉴스 스크랩 AI 분석 / 챗봇 / 이미지 생성이 이 키를 공유)
GEMINI_API_KEY=your_gemini_api_key
# 뉴스 스크랩 AI 분석 모델. 구조화 출력과 url_context 도구를 지원하는 모델이어야 함
GEMINI_API_MODEL=gemini-3.7-flash
```

`GEMINI_API_KEY`가 비어 있으면 뉴스 스크랩의 [AI분석] 버튼만 동작하지 않고,
스크랩 등록·수정·열람 등 나머지 기능은 정상 동작합니다.

### 2. Docker 컨테이너 실행

**로컬 개발 환경 (Windows/WSL2)**

```bash
# 컨테이너 빌드 및 실행
docker compose -f docker-compose.local.yml up -d

# 컨테이너 확인 (ps/logs/exec은 프로젝트 이름으로 찾으므로 -f 불필요)
docker compose ps
```

`docker-compose.local.yml`은 `include`로 `docker-compose.yml`을 끌어오므로 `-f` 하나만
지정하면 됩니다 (Docker Compose 2.20 이상 필요). `vendor`, `storage/framework`, `bootstrap/cache`를 named volume으로 옮겨
WSL2의 9p 파일시스템 왕복을 없애고, php가 준비될 때까지 nginx를 대기시켜 기동 중 502를
방지합니다. **첫 실행은 vendor volume이 비어 있어 `composer install` 전체가 돌기 때문에
수 분 걸립니다.**

캐시를 초기화할 때는 volume까지 함께 지워야 합니다 (`vendor`와 `bootstrap/cache`가
따로 남으면 부팅 시 오류가 날 수 있음):

```bash
docker compose -f docker-compose.local.yml down -v
```

**서버 (Ubuntu)**

서버는 bind mount가 native ext4라 위 최적화의 이득이 없고, named volume이 호스트
파일을 가려 세션 초기화·vendor 재설치 등 부작용만 생깁니다. `docker-compose.local.yml`은
적용하지 마세요.

```bash
docker compose up -d
docker compose ps
```

### 3. Laravel 초기 설정

`composer install`, `php artisan storage:link`, `php artisan migrate --force`는
컨테이너 기동 시 `docker-compose.yml`의 `command`가 매번 자동 실행하므로
수동으로 할 필요가 없습니다. 최초 1회 앱 키 생성만 직접 해주면 됩니다.

```bash
# PHP 컨테이너 접속
docker exec -it php_mqway bash

# 애플리케이션 키 생성 (html/.env의 APP_KEY가 비어 있을 때만)
php artisan key:generate
```

### 4. 접속

브라우저에서 `http://localhost`로 접속합니다. (포트는 `.env`의 `WEB_PORT`)

### 5. 마이그레이션 (배포 시)

컨테이너 이름은 `php_<COMPOSE_PROJECT_NAME>` 형식입니다. (기본값: `php_mqway`)

```bash
# 대기 중인 마이그레이션 실행
docker exec php_mqway php artisan migrate --force

# 적용 상태 확인
docker exec php_mqway php artisan migrate:status

# 마지막 배치 되돌리기
docker exec php_mqway php artisan migrate:rollback --force
```

php 컨테이너는 시작 시 `php artisan migrate --force`를 자동 실행하므로,
컨테이너를 재시작하면 마이그레이션도 함께 적용됩니다.

## 주요 시스템 구성

### 인증 시스템
- 일반 로그인/회원가입
- SNS 로그인 (Google OAuth)
- 회원정보 찾기 (아이디/비밀번호 찾기)
- 비밀번호 변경

### 커뮤니티 게시판
- 자유 게시판
- 추천 콘텐츠 게시판
- 투자 리서치 게시판 (회원 전용)
- 경제 비디오 게시판
- 포트폴리오 게시판
- 뉴스 게시판
- 뉴스 스크랩 게시판 (`/board-scrap`) — AI 분석, 글별 공개/나만보기

### 교육 도구 및 게임
- 경제 용어 게임
- 금융 퀴즈
- 은퇴 계산기
- 캐시플로우 게임

### 가이드북
- 원하는 삶 탐색 (Life Search)
- 현실 점검 (Reality Check)
- 로드맵

### 마이페이지
- 프로필 관리
- 비밀번호 변경
- 내 뉴스 스크랩 (`/board-scrap?mine=1`)
- MQ 맵핑
- 좋아요한 콘텐츠

## AI 기능 (Gemini)

Gemini API를 세 곳에서 사용합니다. API 키는 `GEMINI_API_KEY` 하나를 공유하고,
모델은 기능마다 요구사항이 달라 각각 지정합니다.

| 기능 | 모델 | 위치 |
|------|------|------|
| 뉴스 스크랩 AI 분석 | `GEMINI_API_MODEL` (기본 `gemini-3.7-flash`) | `app/Services/NewsAiAnalyzer.php` |
| 캐시플로우 챗봇 | `GEMINI_API_MODEL` (기본 `gemini-3.7-flash`) | `app/Services/CashflowChatBot.php` |
| MQ 맵핑 이미지 생성 | `gemini-3.1-flash-image-preview` (하드코딩) | `app/Http/Controllers/MyPageController.php` |

키는 반드시 `config('services.gemini.api_key')`로 읽습니다. 설정 파일 밖에서
`env()`를 직접 호출하면 `php artisan config:cache`를 켠 환경에서 `null`이 되어
조용히 실패합니다.

### 뉴스 스크랩 AI 분석

`/board-scrap/create`에서 뉴스 링크를 넣고 **[AI분석]**을 누르면 네 개 파트를
생성해 폼에 채웁니다. 사람이 입력하는 항목은 제목 · 링크 · 선택한 이유 세 개뿐입니다.

1. 뉴스에 대한 짧은 해석
2. 뉴스 본문 내 경제 용어 (최대 10개, 새로 알게 된 용어를 체크해 저장)
3. 향후 전망 (단기 3~6개월 / 중장기 1~3년)
4. 내 경제상황에 맞는 질문 2개

**본문 확보는 2단입니다.** `NewsArticleExtractor`가 서버에서 HTML을 받아 언론사별
컨테이너 XPath로 본문을 뽑고, 실패하면 `NewsAiAnalyzer`가 Gemini `url_context`
도구로 모델이 URL을 직접 읽게 폴백합니다. 어느 경로를 탔는지는 `mq_ai_source`
(`crawl` / `url_context`)에 남습니다. 기사가 아닌 페이지(로그인 · 삭제 · 목록)는
모델이 `articleReadable=false`로 응답하고 사용자에게 안내 문구를 띄웁니다.

네 파트는 **한 번의 호출**로 받습니다 (`responseSchema` 구조화 출력). 파트마다
호출하면 지연과 비용이 4배가 되고 파트 간 톤이 어긋납니다. 파트별 지침은
`NewsAiAnalyzer::taskPrompt()` 안에서 `[파트 1]`~`[파트 4]` 블록으로 나눠 관리하므로,
문구를 손볼 때는 그 메서드만 고치면 됩니다.

AI 결과는 초안이며 저장 전에 네 파트 모두 수정할 수 있습니다. 등록 · 수정 폼은
`resources/views/board_scrap/_ai_form.blade.php`를 공용으로 씁니다.

**개인화 근거는 두 가지뿐입니다.** 사용자가 쓴 "선택한 이유" 평문과 회원 생일로
계산한 연령대입니다. 프로필 항목이 늘어나면 `BoardScrapController::buildUserContext()`에
키를 더하고 `[파트 4]` 지침에 한 줄 추가하면 됩니다. 프롬프트가 `<user_context>`에
있는 값만 쓰도록 지시되어 있어, 키가 없을 때 없는 정보를 추측하지 않습니다.

호출당 응답 시간은 모델과 경로에 따라 4~20초입니다. 외부 LLM 호출이므로
`/board-scrap/ai-analyze` 라우트에 회원당 분당 10회 제한(`throttle:10,1`)을 걸어 뒀습니다.

### 캐시플로우 챗봇

`/cashflow/intro`에서 게임 규칙을 물어보는 챗봇입니다. 텍스트와 **카드 사진**을
함께 받아 스트리밍으로 답합니다. 로그인이 필요하고 `/api/cashflow/chat` 라우트에
분당 20회 제한(`throttle:20,1`)이 걸려 있습니다.

| 역할 | 파일 |
|------|------|
| 모델 호출 · 프롬프트 조립 · SSE 파싱 | `app/Services/CashflowChatBot.php` |
| 참고 자료 로더 | `app/Services/CashflowKnowledgeBase.php` |
| 대화 이력 (세션) | `app/Services/CashflowChatHistory.php` |
| 검증 + SSE 중계 | `app/Http/Controllers/CashflowChatController.php` |
| 모달 마크업 | `resources/views/cashflow/_chatbot.blade.php` |
| 화면 로직 | `public/js/cashflow/chatbot.js` |

**프롬프트와 참고 자료는 코드 밖에 있습니다.** 배포 없이 파일만 고치면 됩니다.

- `resources/prompts/cashflow/system.md` — 시스템 지시문. HTML 주석은 자동으로
  제거되므로 편집 메모를 남겨도 됩니다. 파일이 없거나 비면
  `CashflowChatBot::fallbackSystemPrompt()`로 떨어집니다.
- `resources/knowledge/cashflow/*.md` — 답변 근거 자료. 파일명 오름차순으로 읽어
  `<document name="...">`로 감싼 `<reference>` 블록을 시스템 지시문 뒤에 붙입니다.
  `README.md`와 `_`로 시작하는 파일은 제외하고, 총합 상한은 6만 자입니다
  (`CashflowKnowledgeBase::MAX_CHARS`). 자세한 규칙은 그 폴더의 `README.md` 참고.

**스트리밍은 `alt=sse`로 받습니다.** 이 파라미터가 없으면 Gemini가 JSON 배열을
쪼개서 흘려보내 청크가 객체 중간을 가를 때 파싱이 깨집니다. 브라우저에는 Gemini
원본이 아니라 자체 봉투(`{"type":"delta"|"done"|"error"}`)만 내보내므로, 모델이나
API 버전이 바뀌어도 화면 코드는 손대지 않아도 됩니다.

**대화 이력은 세션에만** 담고 DB에는 남기지 않습니다. Gemini 규격(`user` / `model`)
그대로 저장해 변환 단계를 없앴고, 최대 8턴까지 유지합니다. 이미지는 이력에 담지
않고 표식만 남깁니다(base64 한 장이 세션 파일을 감당하지 못합니다).

**이미지**는 프런트가 canvas로 최대 1280px JPEG로 정규화해 보내고, 서버가
`data:` URI의 mime을 화이트리스트(JPEG/PNG/WEBP/HEIC)와 대조한 뒤
`getimagesizefromstring()`으로 실제 바이트까지 확인합니다. 상한은 4MB입니다.

게임 상태(`mq_cashflow_games` / `assets` / `liabilities`) 연동은 자리만 열어 뒀습니다.
`CashflowChatController::buildContext()`가 배열을 돌려주고
`CashflowChatBot::buildContextBlock()`이 `<user_context>`로 만들므로, 두 메서드만
채우면 됩니다. 값을 넣을 때는 프롬프트에도 그 수치를 어떻게 쓸지 한 줄 추가해야
모델이 무시하지 않습니다.

## 개발 환경

### 컨테이너 구성

- **Nginx**: 웹서버 (포트 80)
- **PHP-FPM**: PHP 7.2 기반 애플리케이션 서버
- **MySQL**: 데이터베이스 서버 (포트 3306)

### 포트 설정

| 서비스 | 포트 | 설명 |
|--------|------|------|
| Nginx | 80 | 웹 서버 |
| MySQL | 3306 | 데이터베이스 (외부 접속 가능) |

### 볼륨 마운트

- `./html` → `/var/www/html` (애플리케이션 코드)
- `./data/mysql` → `/var/lib/mysql` (데이터베이스 데이터)
- `./nginx.conf` → `/etc/nginx/nginx.conf` (Nginx 설정)
- `./my.cnf` → `/etc/mysql/conf.d/my.cnf` (MySQL 설정)
