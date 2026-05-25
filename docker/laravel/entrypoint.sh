#!/bin/sh
set -e

# Fix permissions for storage directories (mount from host may have different ownership)
if [ -d /var/www/storage/app ]; then
    chown -R www-data:www-data /var/www/storage/app
    chmod -R 775 /var/www/storage/app
fi

# Laravel storage link
if [ ! -L /var/www/public/storage ]; then
    php /var/www/artisan storage:link || true
fi

exec "$@"
