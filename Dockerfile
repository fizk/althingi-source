FROM php:8.1.8-apache-bullseye

ARG ENV
ENV PATH="/var/www/alias:/var/www/bin:${PATH}"

EXPOSE 80

# Configures the operating system and installs
# extensions for the PHP environment.
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
        librdkafka-dev \
        libcurl4-openssl-dev  \
        pkg-config  \
        libsasl2-dev  \
        libpcre3-dev; \
    pecl install -o -f rdkafka-6.0.0; \
    rm -rf /tmp/pear; \
    docker-php-ext-configure opcache --enable-opcache; \
    docker-php-ext-install opcache; \
    docker-php-ext-install zip; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install bcmath; \
    docker-php-ext-install sockets; \
    echo "extension=rdkafka.so" >> /usr/local/etc/php/conf.d/rdkafka.ini; \
    a2enmod rewrite; \
    mv /var/www/html /var/www/public; \
    mkdir -p /var/www/.composer; \
    chown www-data /var/www/.composer; \
    chown www-data /var/www;

# Configures the PHP environment as well as
# the Apache HTTP server.
RUN echo "[PHP]\n\
memory_limit = 2048M \n\
upload_max_filesize = 512M \n\
expose_php = Off \n\n\
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

# If Production, configures PHP's JIT
# environment and sets a resonable buffer size and memory.
RUN if [ "$ENV" = "production" ] ; then \
    echo "opcache.enable=1\n\
opcache.jit_buffer_size=100M\n\
opcache.jit=1255\n" >> /usr/local/etc/php/conf.d/php.ini; \
    fi ;

# Restarts Apache for configuration to take effect
RUN a2enmod rewrite && service apache2 restart;

# If not Production, sets up Xdebug and configures it
# so that the host system can listen to the debugger on
# the host's localhost.
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

# Sets the working directory and the user to www-data.
# That is the user that is already configured to run Apache
WORKDIR /var/www

# If not Production, add scripts to run `phpunit` and `cover`
RUN if [ "$ENV" != "production" ] ; then \
    mkdir -p /var/www/alias; \
    echo "#!/bin/bash\n/var/www/vendor/bin/phpunit \$@" >> /var/www/alias/phpunit; \
    chmod u+x /var/www/alias/phpunit; \
    echo "#!/bin/bash\nXDEBUG_MODE=coverage /var/www/vendor/bin/phpunit --coverage-html=/var/www/tests/docs" >> /var/www/alias/cover; \
    chmod u+x /var/www/alias/cover; \
    fi ;


# Copy dependenices for Composer into container
# Install Composer and then install Coposer dependencies
# based off of it the mode is Production or Development
COPY --chown=www-data:www-data ./composer.json ./composer.json
COPY --chown=www-data:www-data ./composer.lock ./composer.lock

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer --version=2.7.6

RUN if [ "$ENV" != "production" ] ; then \
    composer install --no-interaction --no-cache \
    && composer dump-autoload; \
fi ;

RUN if [ "$ENV" = "production" ] ; then \
    composer install --no-interaction --no-dev --no-cache -o \
    && composer dump-autoload -o; \
fi ;

USER www-data

# Copy source-code into container
# as www-data user
COPY --chown=www-data:www-data ./public ./public
COPY --chown=www-data:www-data ./src ./src
COPY --chown=www-data:www-data ./config ./config
COPY --chown=www-data:www-data ./bin ./bin
