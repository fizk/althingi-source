# #####################################################
#
#   Apache PHP setup
#
# #####################################################

FROM php:7.2.9-apache

RUN apt-get update \
 && apt-get install -y git zlib1g-dev vim \
 && docker-php-ext-install zip \
 && docker-php-ext-install pdo_mysql \
 && a2enmod rewrite \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/apache2.conf \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

COPY ./assets/php/php.ini /usr/local/etc/php/

EXPOSE 80

ENV APPLICATION_ENVIRONMENT production

ENV DB_HOST localhost
ENV DB_PORT 3306
ENV DB_NAME althingi
ENV DB_USER root
#ENV DB_PASSWORD

ENV SEARCH none
#   | elasticsearch | none

ENV ES_HOST localhost
ENV ES_PROTO http
ENV ES_PORT 9200
ENV ES_USER elastic
ENV ES_PASSWORD changeme

ENV LOG_PATH php://stdout
ENV LOG_FORMAT line
#    | logstash | json | line | color | none

ENV CACHE_TYPE none
#    | file (path ./data/cache) | memory | none
ENV CACHE_HOST localhost
ENV CACHE_PORT 6379

# with x-debug version
#RUN pecl install -o -f redis \
#    && pecl install xdebug-2.6.0 \
#    && rm -rf /tmp/pear \
#    && docker-php-ext-enable redis xdebug

# - - -  Option 1
COPY . /var/www
WORKDIR /var/www
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






