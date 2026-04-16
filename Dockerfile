FROM php:8.2-apache

# Installer extensions MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80