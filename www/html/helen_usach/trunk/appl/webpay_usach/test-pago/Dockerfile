FROM php:7.4-cli

RUN apt-get update && apt-get install -qqy git unzip build-essential libaio1 libzip-dev libxml2-dev
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /test-pago
COPY . .
RUN composer install

CMD php -S 0.0.0.0:8080
EXPOSE 8080
