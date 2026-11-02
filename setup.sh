#!/bin/bash

set -euo pipefail
cd "$(dirname "$0")"

echo "🚀 Booking System Setup"
echo "========================"

if ! command -v docker &> /dev/null; then
    echo "❌ Docker не знайдено. Встановіть Docker Desktop."
    exit 1
fi

docker compose version > /dev/null

if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "Configure database credentials in .env, then run this script again."
    exit 1
fi

if [ ! -f "src/.env" ]; then
    echo "📝 Створення .env файлу..."
    cp src/.env.example src/.env
fi

echo "🐳 Запуск Docker контейнерів..."
docker compose config --quiet
docker compose up -d --build --wait

echo "📦 Встановлення залежностей..."
docker compose exec app composer install
docker compose exec app npm ci

if grep -Eq "^APP_KEY=[[:space:]\"']*$" src/.env; then
    docker compose exec app php artisan key:generate
fi

echo "🗄️ Виконання міграцій..."
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
docker compose exec app npm run build

echo ""
echo "✅ Setup завершено!"
echo "========================"
echo "🌐 Сайт: http://localhost"
echo "📊 phpMyAdmin: http://localhost:8080"
echo "📧 Mailhog: http://localhost:8025"
echo ""
echo "Configure optional demo accounts in src/.env; see README.md."
