# Gunakan image resmi PHP dengan Apache
FROM php:8.2-apache

# Install dependencies dan ekstensi PHP (termasuk WebP support untuk GD)
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libwebp-dev libfreetype-dev \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Buat direktori storage dan cache
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/bootstrap/cache

# Copy source code (exclude node_modules dan vendor untuk efisiensi)
COPY . /var/www/html

# Set permission secara rekursif
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html/storage -type d -exec chmod 775 {} \; \
    && find /var/www/html/storage -type f -exec chmod 664 {} \; \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Buat storage link (jika file artisan ada)
RUN if [ -f "/var/www/html/artisan" ]; then \
    php /var/www/html/artisan storage:link; \
    fi

# Ubah document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Aktifkan modul Apache
RUN a2enmod rewrite

# Install dependencies Laravel
RUN if [ -f "/var/www/html/composer.json" ]; then \
    cd /var/www/html && \
    composer install --no-dev --optimize-autoloader --no-interaction; \
    fi

# Health check untuk memastikan storage link ada
HEALTHCHECK --interval=30s --timeout=3s \
  CMD [ -L "/var/www/html/public/storage" ] || exit 1

# Expose port 80
EXPOSE 80

# Volume untuk persistensi data
VOLUME /var/www/html/storage/app/public
VOLUME /var/www/html/storage/framework
VOLUME /var/www/html/storage/logs
