FROM php:8.3-apache

# Instalar dependencias del sistema y extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev libpng-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd \
    && a2enmod rewrite

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar únicamente los archivos de dependencias primero (aprovecha la cache de Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copiar el resto del código de la aplicación
COPY . .

RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache debe servir desde la carpeta public/ de Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Cambiar el puerto de Apache de 80 a 8000
RUN sed -i 's/80/8000/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:8000/g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8000