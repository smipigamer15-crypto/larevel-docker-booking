.PHONY: help build up down restart logs shell migrate fresh seed test clean

help:
	@echo "Available commands:"
	@echo "  make build     - Build Docker images"
	@echo "  make up        - Start all containers"
	@echo "  make down      - Stop all containers"
	@echo "  make restart   - Restart all containers"
	@echo "  make logs      - Show logs"
	@echo "  make shell     - Enter app container"
	@echo "  make migrate   - Run migrations"
	@echo "  make fresh     - Fresh migrations with seed"
	@echo "  make seed      - Run seeders"
	@echo "  make clean     - Remove all containers and volumes"

build:
	docker-compose build --no-cache

up:
	docker-compose up -d
	@echo "⏳ Waiting for services to start..."
	sleep 5
	@echo "✅ Services are running!"
	@echo "  - Website: http://localhost"
	@echo "  - phpMyAdmin: http://localhost:8080"
	@echo "  - Mailhog: http://localhost:8025"

down:
	docker-compose down

restart: down up

logs:
	docker-compose logs -f

shell:
	docker-compose exec app bash

migrate:
	docker-compose exec app php artisan migrate

fresh:
	docker-compose exec app php artisan migrate:fresh --seed

seed:
	docker-compose exec app php artisan db:seed

clean:
	docker-compose down -v
	docker system prune -f

install:
	docker-compose exec app composer install
	docker-compose exec app npm install
	docker-compose exec app npm run build
	docker-compose exec app php artisan key:generate --force
	docker-compose exec app php artisan storage:link
	docker-compose exec app php artisan migrate --seed

test:
	docker-compose exec app php artisan test

artisan:
	docker-compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

%:
	@:
