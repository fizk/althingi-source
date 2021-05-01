<?php

chdir(dirname(__DIR__));
include __DIR__ . '/../vendor/autoload.php';

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\ServiceManager\ServiceManager;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Althingi\Router\RouteInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ConsoleRequest implements ServerRequestInterface
{
    private $uri;

    public function __construct(UriInterface $uri)
    {
        $this->uri = $uri;
    }

    public function getServerParams()
    {
        return [];
    }

    public function getCookieParams()
    {
        return [];
    }

    public function withCookieParams(array $cookies)
    {
        return $this;
    }

    public function getQueryParams()
    {
        return [];
    }

    public function withQueryParams(array $query)
    {
        return $this;
    }

    public function getUploadedFiles()
    {
        return [];
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        return $this;
    }

    public function getParsedBody()
    {
        return null;
    }

    public function withParsedBody($data)
    {
        return $this;
    }

    public function getAttributes()
    {
        return [];
    }

    public function getAttribute($name, $default = null)
    {
        return null;
    }

    public function withAttribute($name, $value)
    {
        return $this;
    }

    public function withoutAttribute($name)
    {
        return $this;
    }

    public function getRequestTarget()
    {
        return '/';
    }

    public function withRequestTarget($requestTarget)
    {
        return $this;
    }

    public function getMethod()
    {
        return 'GET';
    }

    public function withMethod($method)
    {
        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $this->uri = $uri;
        return $this;
    }
    public function getProtocolVersion()
    {
        return '';
    }
    public function withProtocolVersion($version)
    {
        return $this;
    }
    public function getHeaders()
    {
        return [];
    }
    public function hasHeader($name)
    {
        return false;
    }
    public function getHeader($name)
    {
        return null;
    }
    public function getHeaderLine($name)
    {
        return '';
    }
    public function withHeader($name, $value)
    {
        return $this;
    }
    public function withAddedHeader($name, $value)
    {
        return $this;
    }
    public function withoutHeader($name)
    {
        return $this;
    }
    public function getBody()
    {
        return null;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        return $this;
    }
}

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
