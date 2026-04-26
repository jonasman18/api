FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip curl \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite \
    && apt-get clean

# ✅ Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# ✅ Copier d'abord composer.json
COPY composer.json /var/www/html/composer.json

# ✅ Installer les dépendances
RUN cd /var/www/html && composer install --no-interaction --no-dev --optimize-autoloader

# ✅ Copier tout le projet
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80