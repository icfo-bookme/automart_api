# Step 1: PHP FPM ইমেজ
FROM php:8.2-fpm

# Step 2: Working directory
WORKDIR /var/www

# Step 3: System dependencies install
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Step 4: Composer install
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Step 5: Copy Laravel app
COPY . /var/www

# Step 6: Install PHP dependencies
RUN composer install

# Step 7: Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Step 8: Expose port
EXPOSE 9000

# Step 9: Start PHP-FPM
CMD ["php-fpm"]
