FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libonig-dev \
    librabbitmq-dev \
    curl

RUN docker-php-ext-install pdo_mysql mbstring pcntl \
    && pecl install amqp xdebug \
    && docker-php-ext-enable amqp xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY src/ .
RUN composer install

RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]