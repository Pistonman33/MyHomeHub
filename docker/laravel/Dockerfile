# PHP 8.3
FROM php:8.3-fpm

# Install dependencies for Laravel
RUN apt-get update && apt-get install -y \
    bash \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libonig-dev \
    libicu-dev \
    curl \
    libxml2-dev \
    default-mysql-client \
    libzip-dev \
    rsync \
    grep \
    protobuf-compiler \
    protobuf-compiler-grpc \
    libgrpc-dev \
    && pecl install grpc \
    && docker-php-ext-enable grpc \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd intl zip opcache soap

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