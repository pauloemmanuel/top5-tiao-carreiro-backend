# Dockerfile para Laravel API
FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copia composer.json e composer.lock primeiro para cache do Docker
COPY composer*.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

# Copia o resto da aplicação
COPY . /var/www

# Finaliza a instalação do Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissões
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
