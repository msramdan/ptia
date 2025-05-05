# Gunakan image resmi PHP dengan Apache
FROM php:8.2-apache

# Install dependencies dan ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy source code
COPY . /var/www/html

# Ubah document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# Aktifkan modul Apache
RUN a2enmod rewrite

# Set permission
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
