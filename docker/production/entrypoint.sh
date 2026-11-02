#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
    echo 'APP_KEY must be configured on the server; it is never generated during deployment.' >&2
    exit 1
fi

mkdir -p storage/app/private storage/app/public storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/uploads

if [ "$(id -u)" = '0' ]; then
    chown www-data:www-data storage storage/app storage/app/private storage/app/public \
        storage/framework storage/framework/cache storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/uploads
fi

php artisan config:cache --no-ansi
exec docker-php-entrypoint "$@"
