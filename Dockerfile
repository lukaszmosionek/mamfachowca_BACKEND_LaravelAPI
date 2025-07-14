FROM php:8.2-apache

# Zainstaluj potrzebne rozszerzenia PHP
RUN docker-php-ext-install pdo pdo_sqlite

# Skopiuj pliki aplikacji
COPY . /var/www/html

# Ustaw folder jako root Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Zainstaluj Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Przejdź do katalogu projektu i zainstaluj zależności
WORKDIR /var/www/html
RUN composer install --no-interaction --no-scripts --prefer-dist
