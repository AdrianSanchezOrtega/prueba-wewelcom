FROM php:8.2-apache

# Instala extensiones necesarias
RUN apt-get update \
    && apt-get install -y libicu-dev libpq-dev libzip-dev zip git unzip \
    && docker-php-ext-install intl pdo pdo_mysql pdo_pgsql zip

# Habilita mod_rewrite
RUN a2enmod rewrite

# Copia el código fuente
COPY . /var/www/html

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Crea la carpeta var si no existe y da permisos
RUN mkdir -p var \
    && chown -R www-data:www-data var

# Configura Apache para Symfony
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# Permite Composer como root y evita scripts automáticos
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Ejecuta migraciones Doctrine automáticamente durante el build
RUN php bin/console doctrine:migrations:migrate || true

# Expone el puerto 10000 para Render
EXPOSE 10000

# Cambia el puerto de Apache a 10000 para Render
RUN sed -i 's/80/10000/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf
