FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip \
  curl \
  git \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Permissões: ensure directories exist before changing ownership
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
  && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
ENTRYPOINT ["/bin/sh", "-c", "mkdir -p /var/www/storage /var/www/bootstrap/cache && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && exec php-fpm"]
