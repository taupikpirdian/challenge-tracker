FROM php:8.3-fpm-alpine AS challenge-tracker-app

# Install system dependencies
RUN apk add --no-cache \
    bash curl git icu-dev libzip-dev oniguruma-dev zlib-dev \
    freetype-dev libpng-dev libjpeg-turbo-dev

# Install PHP extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        bcmath \
        zip \
        exif \
        pcntl \
        intl

# Optional image extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Enable opcache
RUN docker-php-ext-install opcache \
    && echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock ./

# Install dependencies (production) without executing scripts
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# Copy application source
COPY . .

# Clear config cache (file-based only, no database access)
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan config:cache \
    && composer dump-autoload

# Publish Livewire assets
RUN php artisan vendor:publish --tag=livewire:assets --force

# Set proper ownership for all application files
RUN chown -R www-data:www-data /var/www/html

# Ensure writable permissions for storage and cache
RUN mkdir -p storage bootstrap/cache storage/logs \
    && chmod -R 775 storage bootstrap/cache \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

EXPOSE 9000

CMD ["php-fpm"]

# -----------------------------
# Nginx stage with bundled config
# -----------------------------
FROM nginx:alpine AS challenge-tracker-nginx

# Create log directories
RUN mkdir -p /var/log/nginx /var/log/php-fpm

# Copy entire application (not just public) for Laravel to work properly
COPY --from=challenge-tracker-app /var/www/html /var/www/html

# Set proper permissions
RUN chown -R nginx:nginx /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Bundle nginx configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf