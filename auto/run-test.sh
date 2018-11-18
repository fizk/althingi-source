#!/usr/bin/env bash

while ! mysqladmin ping -h database --silent; do
    echo "Retrying to connect to MySQL in 1"
    sleep 1
done

/usr/src/vendor/bin/phpunit && /usr/src/vendor/bin/phpcs
