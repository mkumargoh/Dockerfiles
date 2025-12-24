# =========================
# Builder Stage
# =========================
FROM php:8.1-apache AS builder

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    default-libmysqlclient-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        mbstring \
        gd \
        bcmath \
        exif \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer safely
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader

# Copy rest of the Snipe-IT source
COPY . .

# =========================
# Production Stage
# =========================
FROM php:8.1-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    default-libmysqlclient-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        mbstring \
        gd \
        bcmath \
        exif \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=builder /var/www/html /var/www/html

# Laravel public directory
RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# Permissions required by Snipe-IT
RUN chown -R www-data:www-data \
    storage bootstrap/cache public/uploads \
    && chmod -R 775 storage bootstrap/cache public/uploads

EXPOSE 80
CMD ["apache2-foreground"]
# End of Dockerfile for PHP application
