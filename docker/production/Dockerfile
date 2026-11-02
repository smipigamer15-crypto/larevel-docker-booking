FROM node:22-bookworm-slim AS assets
WORKDIR /build
COPY src/package.json src/package-lock.json ./
RUN npm ci
COPY src/resources ./resources
COPY src/vite.config.js ./
RUN npm run build

FROM php:8.4-fpm-bookworm AS app
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates curl default-mysql-client git unzip \
    libfreetype6-dev libicu-dev libjpeg62-turbo-dev libonig-dev \
    libpng-dev libwebp-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd intl mbstring opcache pcntl pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY src/ ./
RUN mkdir -p storage/app/private storage/app/public storage/framework/cache/data \
        storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/uploads \
    && composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader \
    && rm -rf tests node_modules public/build \
    && ln -s /var/www/html/storage/app/public public/storage \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads
COPY --from=assets /build/public/build ./public/build
COPY docker/production/entrypoint.sh /usr/local/bin/booking-entrypoint
RUN chmod +x /usr/local/bin/booking-entrypoint
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr
ENTRYPOINT ["booking-entrypoint"]
CMD ["php-fpm"]

FROM nginx:stable-alpine AS web
COPY docker/production/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
HEALTHCHECK --interval=15s --timeout=5s --start-period=30s --retries=6 \
    CMD wget -q -O /dev/null http://127.0.0.1/up || exit 1
