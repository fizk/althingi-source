#!/bin/bash

composer install \
&& cd ./tests && ../vendor/bin/phpunit
