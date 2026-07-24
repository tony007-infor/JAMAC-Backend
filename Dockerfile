# ==========================================
# ETAPA 1: Construcción (Builder)
# ==========================================
FROM composer:2 AS vendor
WORKDIR /app

# Copiar dependencias y ejecutarlas sin requerir la base de datos de PHP local
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Copiar el código fuente y generar el autoload optimizado
COPY . .
RUN composer dump-autoload --optimize

# ==========================================
# ETAPA 2:Imagen final
# ==========================================
FROM php:8.3-apache

# 1. Instalar SOLO lo necesario
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev libzip-dev libpng-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip gd \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# 2. Traer el código limpio y listo desde la Etapa 1
COPY --from=vendor /app /var/www/html

# 3. Asignar permisos seguros para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Configurar el puerto y el DocumentRoot de Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf \
    && sed -i 's/80/8000/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:8000/g' /etc/apache2/sites-available/000-default.conf

EXPOSE 8000