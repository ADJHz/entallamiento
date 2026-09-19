#!/bin/sh
set -eu

cd /var/www/html

database_url="${DB_URL:-${DATABASE_URL:-}}"

if [ -z "${DB_CONNECTION:-}" ]; then
    case "$database_url" in
        postgres://*|postgresql://*)
            export DB_CONNECTION=pgsql
            ;;
    esac
fi

mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

php artisan package:discover --ansi
php artisan migrate --force
php artisan storage:link --force || true
php artisan config:cache
php artisan view:cache

exec apache2-foreground
