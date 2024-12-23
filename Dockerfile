FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Seoul

RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    software-properties-common \
    && add-apt-repository ppa:ondrej/php

RUN apt-get update && apt-get install -y \
    php7.2-fpm \
    php7.2-cli \
    php7.2-mysql \
    php7.2-mbstring \
    php7.2-xml \
    php7.2-zip \
    php7.2-bcmath \
    php7.2-curl

# Composer 2.2 버전 설치
RUN curl -sS https://getcomposer.org/installer | php -- --version=2.2.18 --install-dir=/usr/local/bin --filename=composer

# PHP-FPM 설정
RUN mkdir -p /run/php && \
    sed -i 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/' /etc/php/7.2/fpm/php.ini && \
    sed -i 's/listen = \/run\/php\/php7.2-fpm.sock/listen = 9000/' /etc/php/7.2/fpm/pool.d/www.conf

# 작업 디렉토리 설정
WORKDIR /var/www/html

CMD ["php-fpm7.2", "-F"]