#!/bin/bash

echo "🔍 Перевірка статусу Booking System"
echo "=================================="

echo "📦 Статус контейнерів:"
docker compose ps

echo ""
echo "🌐 Перевірка сайту:"
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200"; then
    echo "✅ Сайт працює (http://localhost)"
else
    echo "❌ Сайт не відповідає"
fi

echo ""
echo "🗄️ Перевірка бази даних:"
if docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mysql -u "$MYSQL_USER" -D "$MYSQL_DATABASE" -e "SELECT 1"' &>/dev/null; then
    echo "✅ База даних працює"
else
    echo "❌ База даних не відповідає"
fi

echo ""
echo "📊 phpMyAdmin: http://localhost:8080"
echo "📧 Mailhog: http://localhost:8025"
