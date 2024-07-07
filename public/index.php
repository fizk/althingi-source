<?php

chdir(dirname(__DIR__));
include __DIR__ . '/../vendor/autoload.php';

set_error_handler('exception_error_handler');

use Althingi\Events\{RequestSuccessEvent, RequestFailureEvent, RequestUnsuccessEvent, RequestWarningEvent};
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Althingi\Router\RouteInterface;
use Althingi\Utils\ConsoleRequest;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Uri;
use Library\Container\Container;



$path = isset($argv[1]) ? $argv[1] : '/';

mb_parse_str(read_resource(fopen("php://input", "r")), $bodyQuery);

$request = php_sapi_name() === 'cli'
    ? new ConsoleRequest(new Uri('/'.$path))
    : ServerRequestFactory::fromGlobals(
        $_SERVER,
        $_GET,
        $bodyQuery, //$_POST
        $_COOKIE,
        $_FILES
    );

$emitter = new SapiEmitter();
$manager = new Container(require_once './config/service.php');
$router = $manager->get(RouteInterface::class);

try {
    $routeMatch = $router->match($request);
    if ($routeMatch) {
        $request = $request->withAttribute('matched_route_name', $routeMatch->getMatchedRouteName());
        $controller = $manager->get($routeMatch->getParam('controller'));

        $params = php_sapi_name() === 'cli'
            ? extractCliParams(implode(' ', array_slice($argv, 1)))
            : $routeMatch->getParams();
        foreach($params as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }
        $response = $controller->handle($request);
    } else {
        $response = new EmptyResponse(404);
    }

    $emitter->emit($response);

    if ($response->getStatusCode() === 409) {
        $manager->get(Psr\EventDispatcher\EventDispatcherInterface::class)
            ->dispatch(new RequestWarningEvent($request, $response));
    } else if ($response->getStatusCode() < 400) {
        $manager->get(Psr\EventDispatcher\EventDispatcherInterface::class)
            ->dispatch(new RequestSuccessEvent($request, $response));
    } else {
        $manager->get(Psr\EventDispatcher\EventDispatcherInterface::class)
            ->dispatch(new RequestUnsuccessEvent($request, $response));
    }

} catch (Throwable $error) {
    $event = new RequestFailureEvent($request, $error);
    $emitter->emit(new JsonResponse($event->toJSON(), 500));
    $manager->get(Psr\EventDispatcher\EventDispatcherInterface::class)
        ->dispatch($event);
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

function extractCliParams(string $string): array
{
    $result = [];
    preg_match_all(
        '/(-([a-z]) ([a-zA-Z0-9]*))|(--([a-z_]*)=([a-zA-Z0-9_]*))/',
        $string,
        $result,
        PREG_SET_ORDER
    );

    $return = [];
    foreach($result as $item) {
        $value = array_pop($item);
        $key = array_pop($item);
        $return[$key] = $value;
    }
    return $return;
}
