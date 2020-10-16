# #####################################################
#
#   Apache PHP setup
#
# #####################################################
FROM php:7.4.9-apache

ARG ENV

EXPOSE 80

RUN apt-get update; \
    apt-get install -y \
        zip  \
        unzip \
        libzip-dev \
        git  \
        vim \
        autoconf  \
        g++  \
        make  \
        openssl \
        libssl-dev  \
        libcurl4-openssl-dev  \
        pkg-config  \
        libsasl2-dev  \
        libpcre3-dev; \
    pecl install -o -f redis-4.3.0; \
    pecl install mongodb-1.8.1; \
    rm -rf /tmp/pear; \
    docker-php-ext-enable redis mongodb; \
    docker-php-ext-install zip; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install bcmath; \
    docker-php-ext-install sockets; \
    a2enmod rewrite; \
    mv /var/www/html /var/www/public;

RUN echo "memory_limit = 2048M \n \
    upload_max_filesize = 512M \n \
    date.timezone = Atlantic/Reykjavik \n" > /usr/local/etc/php/conf.d/php.ini

RUN echo "<VirtualHost *:80>\n \
    DocumentRoot /var/www/public\n \
    ErrorLog \${APACHE_LOG_DIR}/error.log\n \
    CustomLog \${APACHE_LOG_DIR}/access.log combined\n \
    RewriteEngine On\n \
    RewriteRule ^index\.php$ - [L]\n \
    RewriteCond %{REQUEST_FILENAME} !-f\n \
    RewriteCond %{REQUEST_FILENAME} !-d\n \
    RewriteRule . /index.php [L]\n \
    </VirtualHost>\n" > /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite && service apache2 restart;

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
RUN mkdir -p ./data/cache

COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock

RUN if [ "$ENV" != "production" ] ; then \
    composer install --prefer-source --no-interaction --no-suggest \
    && composer dump-autoload; \
    fi ;

RUN if [ "$ENV" = "production" ] ; then \
    composer install --prefer-source --no-interaction --no-dev --no-suggest -o \
    && composer dump-autoload -o; \
    fi ;

COPY ./public ./public
COPY ./module ./module
COPY ./config ./config
