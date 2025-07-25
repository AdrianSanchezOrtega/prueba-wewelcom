FROM php:8.2-apache

# Instala extensiones necesarias
RUN apt-get update \
    && apt-get install -y libicu-dev libpq-dev libzip-dev zip git unzip \
    && docker-php-ext-install intl pdo pdo_mysql zip

# Habilita mod_rewrite
RUN a2enmod rewrite

# Copia el código fuente
COPY . /var/www/html

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Da permisos a la carpeta var
RUN chown -R www-data:www-data var

# Configura Apache para Symfony
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
