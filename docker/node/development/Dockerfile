FROM node:18-alpine3.17
RUN apk add --no-cache curl
USER node
WORKDIR /var/www/laravel
HEALTHCHECK --interval=5s --timeout=5s --start-period=1s CMD curl --fail http://127.0.0.1:3000/health || exit 1