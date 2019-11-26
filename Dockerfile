# #####################################################
#
#   Apache PHP setup
#
# #####################################################

FROM php:7.3.11-apache-buster

RUN apt-get update \
 && apt-get install -y zip unzip libzip-dev \
 && apt-get install -y git vim \
 && apt-get install -y autoconf g++ make openssl libssl-dev libcurl4-openssl-dev pkg-config libsasl2-dev libpcre3-dev \
 && echo "alias ll=\"ls -alh\"" >> /root/.bashrc \
 && . /root/.bashrc \
 && docker-php-ext-install zip \
 && docker-php-ext-install pdo_mysql \
 && docker-php-ext-install bcmath \
 && docker-php-ext-install sockets \
 && a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/apache2.conf \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install -o -f redis-4.3.0 \
    && pecl install mongodb-1.6.0 \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis mongodb

ARG WITH_XDEBUG
ARG WITH_DEV

RUN if [ $WITH_XDEBUG = "true" ] ; then \
        pecl install xdebug; \
        docker-php-ext-enable xdebug; \
        echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi ;

COPY ./auto/php/php.ini /usr/local/etc/php/

EXPOSE 80

WORKDIR /var/www

COPY ./composer.json .
COPY ./composer.lock .
COPY ./phpcs.xml .
COPY ./phpunit.xml.dist .

RUN mkdir -p /var/www/data/cache

RUN if [ $WITH_DEV = "true" ] ; then \
        /usr/local/bin/composer install --prefer-source --no-interaction --no-suggest \
            && /usr/local/bin/composer dump-autoload -o; \
    fi ;

RUN if [ $WITH_DEV != "true" ] ; then \
        /usr/local/bin/composer install --prefer-source --no-interaction --no-dev --no-suggest \
            && /usr/local/bin/composer dump-autoload -o; \
    fi ;






