# PHP 8.3 with FPM (light for prod)
FROM php:8.3-fpm-alpine

# Install dependencies for Laravel
RUN apk add --no-cache \
    bash \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    oniguruma-dev \
    icu-dev \
    curl \
    shadow \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define WOR directory
WORKDIR /var/www/

# Laravel Permissions
RUN chown -R www-data:www-data /var/www

USER www-data

USER root

# Entrypoint to create laravel storage links and set permissions
COPY ../laravel/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]