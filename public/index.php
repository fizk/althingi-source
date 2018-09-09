<?php

use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

//var_dump([
//    getenv('DB_HOST'),
//    getenv('DB_PORT'),
//    getenv('DB_NAME'),
//    getenv('DB_USER'),
//    getenv('DB_PASSWORD'),
//
//    getenv('CACHE_HOST'),
//    getenv('CACHE_PORT'),
//    getenv('CACHE_TYPE'),
//
//    getenv('SEARCH'),
//
//    getenv('ES_HOST'),
//    getenv('ES_PROTO'),
//    getenv('ES_PORT'),
//    getenv('ES_USER'),
//    getenv('ES_PASSWORD'),
//
//    getenv('LOGGER_PATH_LOGS'),
//    getenv('LOGGER_PATH_ERROR'),
//    getenv('LOGGER_SAVE'),
//    getenv('LOGGER_STREAM'),
//    getenv('LOGGER_FORMAT'),
//]);

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run zf composer install` if you are using Docker.\n"
    );
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}

// Run the application!
Application::init($appConfig)->run();
