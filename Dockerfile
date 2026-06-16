FROM php:8.2-apache

# Installation des dépendances système (zip, git, etc.)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql mysqli

# Active le module de reecriture d'URL, requis par public/.htaccess
RUN a2enmod rewrite

# On récupère Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Remplace le vhost Apache par defaut : DocumentRoot -> public/, AllowOverride All
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
