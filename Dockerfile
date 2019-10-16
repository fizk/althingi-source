# #####################################################
#
#   Apache PHP setup
#
# #####################################################

FROM php:7.2.9-apache

RUN apt-get update \
 && apt-get install -y zip unzip \
 && apt-get install -y git zlib1g-dev vim \
# && apt-get install -y pkg-config \
 && apt-get install -y autoconf g++ make openssl libssl-dev libcurl4-openssl-dev pkg-config libsasl2-dev libpcre3-dev \
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
    && pecl install mongodb-1.5.3 \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis mongodb

COPY ./auto/php/php.ini /usr/local/etc/php/

EXPOSE 80

# with x-debug version
#RUN pecl install -o -f redis \
#    && pecl install xdebug-2.6.0 \
#    && rm -rf /tmp/pear \
#    && docker-php-ext-enable redis xdebug

# - - -  Option 1
WORKDIR /var/www

COPY ./composer.json .
COPY ./composer.lock .
COPY ./phpcs.xml .
COPY ./phpunit.xml.dist .

RUN /usr/local/bin/composer install --prefer-source --no-interaction --no-dev \
    && /usr/local/bin/composer dump-autoload -o
# - - -  end of Option 1

# - - -  Option 2 (https://www.sentinelstand.com/article/composer-install-in-dockerfile-without-breaking-cache)
# COPY composer.json ./var/www
# COPY composer.lock ./var/www
# RUN composer install --no-scripts --no-autoloader
# COPY . ./var/www
# RUN composer dump-autoload --optimize && \
#     composer run-scripts post-install-cmd
# WORKDIR /var/www
# - - -  end of Option 2






