FROM composer:latest as composer
FROM php:8.3-fpm

WORKDIR /app

ENV PHALCON_VERSION="5.6.0"

RUN apt update -y && \
    apt install -y \
        apt-utils \
        gettext \
        git \
        libzip-dev \
        nano \
        sudo \
        wget \
        zip

# PECL Packages
RUN pecl install phalcon-${PHALCON_VERSION}  \
      xdebug

# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Install PHP extensions
RUN docker-php-ext-install \
      gettext \
      zip

# Install PHP extensions
RUN docker-php-ext-enable \
      opcache \
      phalcon \
      xdebug

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Add user
RUN groupadd -g 1000 phalcon
RUN useradd -u 1000 -ms /bin/bash -g phalcon phalcon

COPY --chown=phalcon:phalcon . /app

USER phalcon

HEALTHCHECK --interval=30s --timeout=5s --start-period=1s \
    CMD REQUEST_METHOD=GET SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping cgi-fcgi -bind -connect 127.0.0.1:9000

EXPOSE 9000
CMD ["php-fpm"]
