FROM php:8.2-apache

# Extensiones que CodeIgniter 4 y el proyecto necesitan
RUN apt-get update && apt-get install -y \
        libicu-dev \
        libzip-dev \
        unzip \
        git \
    && docker-php-ext-install intl mysqli pdo_mysql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Apache debe servir desde /public y permitir que .htaccess funcione
COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permisos para que CodeIgniter pueda escribir cache, logs, sesiones, uploads
RUN chown -R www-data:www-data writable \
    && chmod -R 775 writable

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
