# #####################################################
#
#   Apache PHP setup
#
# #####################################################
FROM php:8.0.5-apache-buster

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
    rm -rf /tmp/pear; \
    docker-php-ext-enable redis; \
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

# Apache Kafka
# apt install librdkafka-dev maybe?
ENV LIBRDKAFKA_VERSION 1.6.0
ENV EXT_RDKAFKA_VERSION 5.0.0

RUN git clone --depth 1 --branch v$LIBRDKAFKA_VERSION https://github.com/edenhill/librdkafka.git; \
    cd librdkafka; \
    ./configure; \
    make; \
    make install; \
    pecl channel-update pecl.php.net; \
    pecl install rdkafka-$EXT_RDKAFKA_VERSION; \
    docker-php-ext-enable rdkafka; \
    rm -rf /librdkafka;

RUN a2enmod rewrite && service apache2 restart;

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

RUN if [ "$ENV" != "production" ] ; then \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.mode = debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.idekey=myKey" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi ;

WORKDIR /var/www

COPY ./composer.json ./composer.json
COPY ./composer.lock ./composer.lock

# RUN if [ "$ENV" != "production" ] ; then \
#     composer config -g github-oauth.github.com 6123ac2cdc66febecc9dd6227a6819b01c0a5e66 && \
#     composer install --prefer-source --no-interaction \
#     && composer dump-autoload; \
#     fi ;

# RUN if [ "$ENV" = "production" ] ; then \
#     composer install --prefer-source --no-interaction --no-dev -o \
#     && composer dump-autoload -o; \
#     fi ;

COPY ./public ./public
COPY ./src ./src
COPY ./config ./config
