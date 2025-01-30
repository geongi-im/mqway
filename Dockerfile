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
    php7.2-curl

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

# 권한 설정 및 Composer 설치 스크립트 복사
COPY set_permissions.sh /usr/local/bin/set_permissions.sh
RUN chmod +x /usr/local/bin/set_permissions.sh

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 컨테이너 시작 시 권한 설정 및 Composer 설치 실행
CMD ["bash", "-c", "/usr/local/bin/set_permissions.sh && php-fpm7.2 -F"]
