# Stage 1: PHP Dependencies
FROM php:8.3-fpm-alpine AS composer

WORKDIR /app

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    curl-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    gd \
    intl \
    bcmath \
    xml \
    dom \
    mbstring \
    curl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# ---

# Stage 2: Frontend Assets
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .
RUN npm run build

# ---

# Stage 3: Production Image
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    libpng-dev \
    libxml2-dev \
    oniguruma-dev \
    curl-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    intl \
    bcmath \
    opcache \
    xml \
    dom \
    mbstring \
    curl

# Copy application files
COPY . .

# Remove any existing .env to ensure Render env vars are used
RUN rm -f .env

# Copy composer dependencies from Stage 1
COPY --from=composer /app/vendor ./vendor

# Copy built assets from Stage 2
COPY --from=frontend /app/public/build ./public/build

# Finalize composer (autoload)
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --no-dev

# Set permissions for Nginx and PHP
RUN mkdir -p /var/lib/nginx/tmp /var/log/nginx \
    && chown -R www-data:www-data /var/lib/nginx /var/log/nginx /var/www/html

# Copy Nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Copy PHP-FPM config
COPY fpm-www.conf /usr/local/etc/php-fpm.d/www.conf

# Copy entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
