FROM php:8.2-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git curl libsqlite3-dev unzip \
    && docker-php-ext-install pdo pdo_sqlite

COPY . .

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer install

CMD php artisan serve --host=0.0.0.0 --port=8000
