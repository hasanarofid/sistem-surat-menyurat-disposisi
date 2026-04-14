# Stage 1: Dependency management
FROM php:8.3-fpm-alpine as vendor

WORKDIR /var/www

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git

# Install PHP extensions
RUN docker-php-ext-install pcntl bcmath gd pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (ignoring scripts for now)
RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# Stage 2: Final image
FROM php:8.3-fpm-alpine

WORKDIR /var/www

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pcntl bcmath gd pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application
COPY . .

# Copy vendor from stage 1
COPY --from=vendor /var/www/vendor ./vendor

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
