#!/bin/bash
#
# 마이그레이션 실행 스크립트 (서버/로컬 공통)
#
#   ./migrate.sh            대기 중인 마이그레이션 실행
#   ./migrate.sh status     마이그레이션 적용 상태 확인
#   ./migrate.sh rollback   마지막 배치 되돌리기
#
set -euo pipefail

cd "$(dirname "$0")"

if [ ! -f .env ]; then
    echo "오류: .env 파일이 없습니다. (docker-compose 설정을 읽을 수 없음)" >&2
    exit 1
fi

# .env에서 프로젝트 이름을 읽어 PHP 컨테이너 이름을 구성 (CRLF 대비 \r 제거)
PROJECT_NAME=$(grep -E '^COMPOSE_PROJECT_NAME=' .env | tail -1 | cut -d= -f2- | tr -d '\r"'"'"' ')
if [ -z "$PROJECT_NAME" ]; then
    echo "오류: .env에 COMPOSE_PROJECT_NAME이 설정되어 있지 않습니다." >&2
    exit 1
fi

CONTAINER="php_${PROJECT_NAME}"

if ! docker ps --format '{{.Names}}' | grep -qx "$CONTAINER"; then
    echo "오류: '${CONTAINER}' 컨테이너가 실행 중이 아닙니다." >&2
    echo "      먼저 'docker-compose up -d' 를 실행하세요." >&2
    exit 1
fi

run() {
    docker exec "$CONTAINER" php artisan "$@"
}

case "${1:-migrate}" in
    migrate)
        echo "[${CONTAINER}] 마이그레이션 실행 전 상태"
        run migrate:status
        echo
        echo "[${CONTAINER}] php artisan migrate --force"
        run migrate --force
        echo
        echo "[${CONTAINER}] 실행 후 상태"
        run migrate:status
        ;;
    status)
        run migrate:status
        ;;
    rollback)
        echo "마지막 배치를 되돌립니다. 계속하려면 'yes'를 입력하세요."
        read -r answer
        if [ "$answer" != "yes" ]; then
            echo "취소했습니다."
            exit 0
        fi
        run migrate:rollback --force
        run migrate:status
        ;;
    *)
        echo "사용법: $0 [migrate|status|rollback]" >&2
        exit 1
        ;;
esac
