FROM php:7.2.9

RUN apt-get update \
 && apt-get install -y git zlib1g-dev vim\
 && docker-php-ext-install zip \
 && docker-php-ext-install pdo_mysql \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install -o -f redis \
    && pecl install xdebug-2.6.0 \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis xdebug

COPY ./auto/php/php.ini /usr/local/etc/php/

WORKDIR /var/www
