FROM php:8.2-apache

# Activer mod_rewrite (optionnel mais utile)
RUN a2enmod rewrite

# Copier tous les fichiers dans Apache
COPY . /var/www/html/

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html

# Exposer le port
EXPOSE 80