FROM php:8.0.8-apache-buster

ARG ENV
ENV PATH="/var/www:/var/www/bin:${PATH}"

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
    docker-php-ext-configure opcache --enable-opcache; \
    docker-php-ext-install opcache; \
    docker-php-ext-enable redis; \
    docker-php-ext-install zip; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install bcmath; \
    docker-php-ext-install sockets; \
    a2enmod rewrite; \
    mv /var/www/html /var/www/public; \
    mkdir -p /var/www/.composer; \
    chown www-data /var/www/.composer; \
    chown www-data /var/www;

RUN echo "[PHP]\n\
memory_limit = 2048M \n\
upload_max_filesize = 512M \n\
date.timezone = Atlantic/Reykjavik \n" >> /usr/local/etc/php/conf.d/php.ini; \
echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/public\n\
    ErrorLog \${APACHE_LOG_DIR}/error.log\n\
    CustomLog \${APACHE_LOG_DIR}/access.log combined\n\
    RewriteEngine On\n\
    RewriteRule ^index\.php$ - [L]\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule . /index.php [L]\n\
</VirtualHost>\n" > /etc/apache2/sites-available/000-default.conf;

RUN if [ "$ENV" = "production" ] ; then \
    echo "opcache.enable=1\n\
opcache.jit_buffer_size=100M\n\
opcache.jit=1255\n" >> /usr/local/etc/php/conf.d/php.ini; \
    fi ;

RUN a2enmod rewrite && service apache2 restart;

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
USER www-data

COPY --chown=www-data:www-data ./composer.json ./composer.json
COPY --chown=www-data:www-data ./composer.lock ./composer.lock

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/var/www --filename=composer --version=2.1.3

RUN if [ "$ENV" != "production" ] ; then \
    composer install --prefer-source --no-interaction --no-cache \
    && composer dump-autoload; \
    fi ;

RUN if [ "$ENV" = "production" ] ; then \
    composer install --prefer-source --no-interaction --no-dev --no-cache -o \
    && composer dump-autoload -o; \
    fi ;

COPY --chown=www-data:www-data ./public ./public
COPY --chown=www-data:www-data ./src ./src
COPY --chown=www-data:www-data ./config ./config
