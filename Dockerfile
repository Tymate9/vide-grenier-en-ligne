FROM php:8.2-apache

# Installation des dépendances système (zip, git, etc.)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    msmtp \
    msmtp-mta \
    && docker-php-ext-install zip pdo pdo_mysql mysqli

RUN echo 'sendmail_path = "/usr/bin/msmtp -t"' > /usr/local/etc/php/conf.d/mail.ini

# Active le module de reecriture d'URL, requis par public/.htaccess
RUN a2enmod rewrite

# On récupère Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Remplace le vhost Apache par defaut : DocumentRoot -> public/, AllowOverride All
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
