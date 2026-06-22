#!/bin/sh
set -e

copy_public_assets() {
  if [ ! -f /var/www/public/manifest.json ] || [ ! -f /var/www/public/index.php ] || ! cmp -s /tmp/public-dist/manifest.json /var/www/public/manifest.json; then
    echo "Initializing public volume from built image"
    cp -a /tmp/public-dist/. /var/www/public/
  fi
}

copy_public_assets

# Laravel storage link
if [ ! -L /var/www/public/storage ]; then
    php /var/www/artisan storage:link || true
fi

exec "$@"
