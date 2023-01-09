FROM php:8-fpm
WORKDIR /var/www/html
RUN pecl install -f xdebug
RUN docker-php-ext-enable xdebug
RUN echo "xdebug.mode=coverage" >>/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN apt update
RUN apt -y install git
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
