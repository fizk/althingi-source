<?php

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
#   A script that runs through the router configuration and prints out
#   all API URLs
#
#
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #


error_reporting(E_ALL | E_STRICT);
chdir(__DIR__.'/../');

include __DIR__ . '/../vendor/autoload.php';

$rotes = require_once './config/module.config.php';

function printRoute($routes, $prefix = '', $depth = 0)
{
    foreach ($routes as $key => $value) {
//        echo str_repeat("\t", $depth) . "{$key} :: {$prefix}{$value['options']['route']}\n";
        echo "{$prefix}{$value['options']['route']} {$value['options']['defaults']['controller']}\n";
//        reflectionController($value['options']['defaults']['controller']);
        if (array_key_exists('child_routes', $value)) {
            printRoute($value['child_routes'], $value['options']['route'], $depth+1);
        }
    }
}

function reflectionController($controller)
{
    new $controller;
    $reflectionClass = new ReflectionClass($controller);
    print($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC));
}

printRoute($rotes['router']['routes']);
