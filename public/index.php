<?php

chdir(dirname(__DIR__));
include __DIR__ . '/../vendor/autoload.php';

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\ServiceManager\ServiceManager;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Althingi\Router\RouteInterface;
use Laminas\Diactoros\ServerRequestFactory;

$path = isset($argv[1]) ? $argv[1] : '/';

mb_parse_str(read_resource(fopen("php://input", "r")), $bodyQuery);
$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $bodyQuery, //$_POST
    $_COOKIE,
    $_FILES
);

$manager = new ServiceManager(require_once './config/service.php');
$router = $manager->get(RouteInterface::class);

$emitter = new SapiEmitter();
$routeMatch = $router->match($request);
$request = $request->withAttribute('matched_route_name', $routeMatch->getMatchedRouteName());
$controller = $manager->get($routeMatch->getParam('controller'));

try {
    foreach($routeMatch->getParams() as $key => $value) {
        $request = $request->withAttribute($key, $value);
    }
    $emitter->emit($controller->handle($request));
} catch (Throwable $error) {
    $emitter->emit(new JsonResponse([
        'PROVIDER',
        $request->getMethod(),
        $request->getUri()->__toString(),
        500,
        0,
        $error->getMessage(),
        "{$error->getFile()}:{$error->getLine()} ",
        $error->getTrace(),
    ], 500));
}


function exception_error_handler($severity, $message, $file, $line)
{
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}

function read_resource(/*resource*/$resource): string
{
    $result = '';
    while ($data = fread($resource, 1024)) $result .= $data;
    fclose($resource);

    return $result;
}
