#!/bin/bash

# 디렉토리 생성 및 권한 설정
echo "Setting up storage directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/{cache,sessions,testing,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Laravel 캐시 클리어
echo "Clearing Laravel caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 기존 명령어 실행
exec "$@"
