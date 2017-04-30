<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 4/9/17
 * Time: 8:19 PM
 */


error_reporting(E_ALL | E_STRICT);
chdir(__DIR__.'/../');

include __DIR__ . '/../vendor/autoload.php';

$rotes = require_once './config/module.config.php';

new \Althingi\Controller\IndexController();

print_r($rotes);

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
