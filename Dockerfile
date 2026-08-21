FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Seoul

# 필수 패키지 설치
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    software-properties-common \
    && add-apt-repository ppa:ondrej/php

# PHP 7.2 및 확장 설치
RUN apt-get update && apt-get install -y \
    php7.2-fpm \
    php7.2-cli \
    php7.2-mysql \
    php7.2-mbstring \
    php7.2-xml \
    php7.2-zip \
    php7.2-bcmath \
    php7.2-curl \
    php7.2-gmp \
    php7.2-json \
    php7.2-intl \
    php7.2-gd

# Composer 설치 (2.2 버전)
RUN curl -sS https://getcomposer.org/installer | php -- --version=2.2.18 --install-dir=/usr/local/bin --filename=composer

# PHP-FPM 설정
RUN mkdir -p /run/php && \
    sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' /etc/php/7.2/fpm/php.ini && \
    sed -i 's/listen = \/run\/php\/php7.2-fpm.sock/listen = 9000/' /etc/php/7.2/fpm/pool.d/www.conf && \
    sed -i 's/pm = dynamic/pm = dynamic/' /etc/php/7.2/fpm/pool.d/www.conf && \
    sed -i 's/pm.max_children = 5/pm.max_children = 50/' /etc/php/7.2/fpm/pool.d/www.conf && \
    sed -i 's/pm.start_servers = 2/pm.start_servers = 5/' /etc/php/7.2/fpm/pool.d/www.conf && \
    sed -i 's/pm.min_spare_servers = 1/pm.min_spare_servers = 5/' /etc/php/7.2/fpm/pool.d/www.conf && \
    sed -i 's/pm.max_spare_servers = 3/pm.max_spare_servers = 35/' /etc/php/7.2/fpm/pool.d/www.conf && \
    echo "pm.max_requests = 500" >> /etc/php/7.2/fpm/pool.d/www.conf

# 업로드 관련 설정 (php.ini 기본값 upload_max_filesize 2M / post_max_size 8M)
# 앱 제한은 파일당 5MB(max:5120)이므로 upload_max_filesize에 여유를 둬서
# PHP가 아니라 Laravel 검증이 걸리도록 함. 다중 업로드 총량은 40M.
# 순서: nginx 50M > post_max_size 40M > upload_max_filesize 6M > 앱 5MB
# conf.d는 php.ini 이후에 로드되므로 여기 값이 우선 적용됨. fpm/cli 모두 동일하게 지정.
RUN echo "upload_max_filesize = 6M" > /etc/php/7.2/fpm/conf.d/99-uploads.ini && echo "post_max_size = 40M" >> /etc/php/7.2/fpm/conf.d/99-uploads.ini && cp /etc/php/7.2/fpm/conf.d/99-uploads.ini /etc/php/7.2/cli/conf.d/99-uploads.ini

# 기존 www-data 사용자 설정 부분 수정
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupmod -g ${GROUP_ID} www-data && \
    usermod -u ${USER_ID} -g www-data www-data && \
    mkdir -p /var/www/html/storage/logs && \
    mkdir -p /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/bootstrap/cache

# 권한 설정 및 Composer 설치 스크립트 복사
COPY set_permissions.sh /usr/local/bin/set_permissions.sh
RUN chmod +x /usr/local/bin/set_permissions.sh

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 컨테이너 시작 시 권한 설정 및 Composer 설치 실행
CMD ["bash", "-c", "cd /var/www/html && composer install --no-interaction --no-dev --optimize-autoloader && /usr/local/bin/set_permissions.sh && php-fpm7.2 -F"]
