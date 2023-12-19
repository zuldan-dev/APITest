FROM php:8.2-fpm

RUN apt-get update && apt-get upgrade -y \
	&& apt-get install -y apt-utils

RUN apt-get update && apt-get install -y \
    libldb-dev \
    libldap2-dev \
    libxslt-dev \
    libpq-dev \
    git \
    nano \
    htop \
    libsqlite3-dev \
    libsqlite3-0 \
    libc-client-dev \
    libkrb5-dev \
    curl \
    libcurl4-openssl-dev \
    zip unzip

RUN docker-php-ext-install opcache bcmath calendar
RUN docker-php-ext-install pdo pdo_pgsql pgsql
RUN docker-php-ext-install pdo_mysql pdo_sqlite mysqli

RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libjpeg-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

RUN apt-get install -y \
        libonig-dev \
    && docker-php-ext-install iconv mbstring

RUN apt-get install -y \
        libssl-dev \
    && docker-php-ext-install ftp phar

RUN apt-get install -y \
        libicu-dev \
    && docker-php-ext-install intl

RUN apt-get install -y \
        libmcrypt-dev \
    && docker-php-ext-install session

RUN docker-php-ext-install ctype
RUN docker-php-ext-install dom
RUN docker-php-ext-install sockets

RUN docker-php-ext-install exif \
    fileinfo \
    gettext \
    pcntl \
    posix

RUN apt-get install -y \
        libxml2-dev \
    && docker-php-ext-install xsl simplexml xml xmlwriter

RUN apt-get install -y \
        libzip-dev \
        zlib1g-dev \
    && docker-php-ext-install zip

RUN cd /tmp/ && git clone https://github.com/php-ds/extension "php-ds" \
    && cd php-ds \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && docker-php-ext-enable ds

WORKDIR /www

RUN chown -R www-data:www-data /www

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
