up: docker-up
build: docker-build
down: docker-down
restart: down up
stop: docker-stop
start: docker-start
init: docker-down-clear docker-build docker-up

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-start:
	docker-compose start

docker-stop:
	docker-compose stop

docker-build:
	docker-compose build

app:
	docker-compose run --rm php-cli bash

app-lint:
	docker-compose run --rm php-cli ./vendor/bin/pint

app-clear:
	docker-compose run --rm php-cli php artisan optimize:clear

app-migrate:
	docker-compose run --rm php-cli php artisan migrate

app-update:
	docker-compose run --rm php-cli composer update