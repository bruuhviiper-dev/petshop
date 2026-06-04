.PHONY: install seed test fresh queue build dev

install:
	composer install
	npm ci
	cp -n .env.example .env || true
	php artisan key:generate
	php artisan migrate
	php artisan storage:link
	npm run build

seed:
	php artisan migrate:fresh --seed

test:
	php artisan test --parallel

fresh: seed

queue:
	php artisan queue:work database --sleep=3 --tries=3

build:
	npm run build

dev:
	npm run dev &
	php artisan serve
