# Use PHP 7.4 FPM image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel project (assuming it's in the 'src' directory)
COPY src /var/www

# Install Laravel dependencies (since composer.json is inside 'src')
RUN composer install --no-dev --optimize-autoloader --working-dir=/var/www

# Set permissions for Laravel storage & cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port
EXPOSE 8000

# Start Application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
