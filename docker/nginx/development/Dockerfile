FROM nginx:alpine3.17-slim
RUN apk add --no-cache curl
COPY ./nginx/development/conf.d /etc/nginx/conf.d
WORKDIR /var/www/laravel
HEALTHCHECK --interval=5s --timeout=3s --start-period=1s CMD curl --fail http://127.0.0.1/health || exit 1