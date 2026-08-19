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
│   │   ├── Policies/             # 인가 정책
│   │   ├── Providers/            # 서비스 프로바이더
│   │   └── Traits/               # 공통 트레이트
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
│   │       ├── board/            # 게시판 뷰
│   │       ├── board_content/    # 콘텐츠 게시판
│   │       ├── board_portfolio/  # 포트폴리오 게시판
│   │       ├── board_research/   # 리서치 게시판
│   │       ├── board_video/      # 비디오 게시판
│   │       ├── cashflow/         # 캐시플로우 게임
│   │       ├── course/           # 코스 안내
│   │       ├── emails/           # 이메일 템플릿
│   │       ├── guidebook/        # 가이드북
│   │       ├── layouts/          # 레이아웃 템플릿
│   │       ├── mypage/           # 마이페이지
│   │       ├── news/             # 뉴스
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
```

### 2. Docker 컨테이너 실행

**로컬 개발 환경 (Windows/WSL2)**

```bash
# 컨테이너 빌드 및 실행
docker compose -f docker-compose.yml -f docker-compose.local.yml up -d

# 컨테이너 확인
docker compose ps
```

`docker-compose.local.yml`은 `vendor`, `storage/framework`, `bootstrap/cache`를
named volume으로 옮겨 WSL2의 9p 파일시스템 왕복을 없애고, php가 준비될 때까지
nginx를 대기시켜 기동 중 502를 방지합니다. **첫 실행은 vendor volume이 비어 있어
`composer install` 전체가 돌기 때문에 수 분 걸립니다.**

캐시를 초기화할 때는 volume까지 함께 지워야 합니다 (`vendor`와 `bootstrap/cache`가
따로 남으면 부팅 시 오류가 날 수 있음):

```bash
docker compose -f docker-compose.yml -f docker-compose.local.yml down -v
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
- 뉴스 스크랩
- MQ 맵핑
- 좋아요한 콘텐츠

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
