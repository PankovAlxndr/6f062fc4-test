FROM php:8.2-cli-alpine3.17

ARG UID
ARG GID
ARG USER

ENV XDEBUG_VERSION 3.2.1
ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

RUN apk add --no-cache bash coreutils git linux-headers unzip libzip-dev zip mysql-client freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
    && git clone --branch $XDEBUG_VERSION --depth 1 https://github.com/xdebug/xdebug.git /usr/src/php/ext/xdebug \
    && docker-php-ext-configure xdebug --enable-xdebug-dev \
    && docker-php-ext-install xdebug pdo_mysql zip

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j$(nproc) gd && \
  apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d/security.ini /usr/local/etc/php/conf.d/security.ini
COPY ./common/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY ./common/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

RUN addgroup -g ${GID} --system ${USER} && adduser -G ${USER} --system -D -s /bin/sh -u ${UID} ${USER}

WORKDIR /var/www/laravel
RUN chown ${USER}:${USER} /var/www/laravel

USER ${USER}


