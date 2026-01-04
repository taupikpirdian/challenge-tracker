FROM php:8.3-fpm

WORKDIR /var/www

# System deps
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files FIRST
COPY composer.json composer.lock ./

# Install vendor
RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Copy app source
COPY . .

# Clear package cache to avoid missing dev providers
RUN rm -f bootstrap/cache/packages.php \
    && php artisan package:discover --ansi || true

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]