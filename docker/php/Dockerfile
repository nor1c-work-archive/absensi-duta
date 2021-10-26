FROM php:5.6.32-fpm

WORKDIR /var/www
COPY ./src /var/www

RUN apt-get update && \
    apt-get install -y \
    git \
    zip \
    curl

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli

EXPOSE 9000
CMD ["php-fpm"]