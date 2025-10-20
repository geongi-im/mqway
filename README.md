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

```bash
# 컨테이너 빌드 및 실행
docker-compose up -d

# 컨테이너 확인
docker-compose ps
```

### 3. Laravel 초기 설정

```bash
# PHP 컨테이너 접속
docker exec -it php_mqway bash

# 애플리케이션 키 생성
php artisan key:generate

# 데이터베이스 마이그레이션
php artisan migrate

# 스토리지 링크 생성
php artisan storage:link
```

### 4. 접속

브라우저에서 `http://localhost`로 접속합니다.

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
- MQ 매핑
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
