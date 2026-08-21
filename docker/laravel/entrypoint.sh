#!/bin/sh
set -e

echo "Synchronizing public files from the image"
cp -a /tmp/public-dist/. /var/www/public/

# Laravel storage link
if [ ! -L /var/www/public/storage ]; then
    php /var/www/artisan storage:link || true
fi

exec "$@"
