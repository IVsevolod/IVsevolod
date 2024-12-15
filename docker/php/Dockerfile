FROM php:8.4-fpm

# Install modules

RUN apt-get update \
  && apt-get install -y \
             vim \
             libfreetype6-dev \
             libjpeg62-turbo-dev \
             libmcrypt-dev \
             libpng-dev \
             zlib1g-dev \
             libxml2-dev \
             libzip-dev \
             libonig-dev \
             graphviz \
             libcurl4-openssl-dev \
             pkg-config \
             libpq-dev

RUN apt-get update && apt-get install -y \
        wget \
        git \
        cron \
        mc \
            --no-install-recommends

# Install Xdebug
RUN pecl channel-update pecl.php.net \
    && pecl install -o -f xdebug-3.1.5 \
    && rm -rf /tmp/pear

RUN docker-php-ext-install intl \
  && docker-php-ext-install zip \
  && docker-php-ext-install exif \
  && docker-php-ext-install opcache \
  && docker-php-source delete

# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

RUN apt-get update && apt-get install -y \
      libmagickwand-dev --no-install-recommends \
      && pecl install imagick \
  	&& docker-php-ext-enable imagick


COPY ./www.conf /usr/local/etc/php/

RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/*

RUN usermod -u 1000 www-data

ENV PHP_INI_SCAN_DIR /usr/local/etc/php/local-conf.d:

WORKDIR /app

EXPOSE 9000
CMD ["php-fpm"]
