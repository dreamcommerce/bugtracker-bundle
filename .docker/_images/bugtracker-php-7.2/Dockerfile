FROM php:7.2-cli

RUN apt-get update
RUN apt-get install -y \
    curl \
    libmcrypt-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    zlib1g-dev \
    libzip-dev \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath \
    && pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
