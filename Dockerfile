FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite \
    && apt-get clean

# ✅ Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Copier le projet
COPY . /var/www/html/

# ✅ Installer firebase/php-jwt
RUN cd /var/www/html && composer require firebase/php-jwt:^6.10 --no-interaction

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80