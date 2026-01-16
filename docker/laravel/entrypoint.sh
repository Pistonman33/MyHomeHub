#!/bin/sh
set -e

# Laravel storage link
if [ ! -L /var/www/public/storage ]; then
    php /var/www/artisan storage:link || true
fi

exec "$@"
