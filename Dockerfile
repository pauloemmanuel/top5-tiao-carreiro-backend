# Dockerfile para Laravel API (PHP-FPM)
FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update && apt-get install -y \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip \
  curl \
  git \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY ./api /var/www

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permissões
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
ENTRYPOINT ["/bin/sh", "-c", "chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && exec php-fpm"]
