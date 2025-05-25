FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libonig-dev \
    librabbitmq-dev \
    curl \
    git \
    supervisor

RUN docker-php-ext-install pdo_mysql mbstring pcntl sockets \
    && pecl install amqp xdebug \
    && docker-php-ext-enable amqp xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY src/ .
COPY supervisor/supervisor.conf /etc/supervisor/conf.d/laravel.conf

RUN composer install --no-interaction --prefer-dist

RUN mkdir -p /var/www/storage/logs
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/laravel.conf"]