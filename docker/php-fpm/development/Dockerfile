FROM php:8.2-fpm-alpine3.17

ARG UID
ARG GID
ARG USER

ENV XDEBUG_VERSION 3.2.1
ENV UID=${UID}
ENV GID=${GID}
ENV USER=${USER}

RUN apk add --no-cache fcgi git linux-headers \
    && git clone --branch $XDEBUG_VERSION --depth 1 https://github.com/xdebug/xdebug.git /usr/src/php/ext/xdebug \
    && docker-php-ext-configure xdebug --enable-xdebug-dev \
    && docker-php-ext-install xdebug pdo_mysql pcntl \
    && apk del git

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d/security.ini /usr/local/etc/php/conf.d/security.ini
COPY ./common/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY ./common/php/conf.d/timeouts.ini /usr/local/etc/php/conf.d/timeouts.ini
COPY ./common/php/php-fpm.d/ping.conf /usr/local/etc/php-fpm.d/ping.conf

COPY ./php-fpm/development/entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

RUN addgroup -g ${GID} --system ${USER} && adduser -G ${USER} --system -D -s /bin/sh -u ${UID} ${USER}
RUN sed -i "s/user = www-data/user = '${USER}'/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = '${USER}'/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s \
    CMD REDIRECT_STATUS=true SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
    cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1

WORKDIR /var/www/laravel
RUN chown ${USER}:${USER} /var/www/laravel