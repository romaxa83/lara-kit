FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
	curl \
	wget \
	unzip \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
    libwebp-dev \
	libpng-dev \
    libcurl4-openssl-dev \
    libpcre3-dev \
    libssl-dev \
	libxslt-dev \
	libicu-dev \
	libmcrypt-dev \
	libxml2-dev \
	libzip-dev \
	libonig-dev \
	libpq-dev \
	poppler-utils \
	ffmpeg \
	sox

RUN docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql pgsql pdo_pgsql zip calendar opcache
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl xsl soap exif zip pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD ./docker/conf/php/php.ini /usr/local/etc/php/php.ini

RUN pecl install redis swoole xdebug
RUN pecl install raphf && docker-php-ext-enable raphf
RUN pecl install pecl_http
RUN docker-php-ext-enable redis pcntl xdebug \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log_level = 0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode = debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey = \"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
