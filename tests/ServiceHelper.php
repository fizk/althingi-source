<?php

namespace Althingi;

use Psr\Http\Message\ResponseInterface;
use Mockery\MockInterface;
use Laminas\Http\Request;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Diactoros\{
    Uri,
    ServerRequest
};
use Althingi\Router\{
    RouteMatch,
    RouteInterface
};

trait ServiceHelper
{
    private array $services = [];
    private $controller;
    private Request $request;
    private ResponseInterface $response;
    private ServiceManager $serviceManager;
    private RouteInterface $router;
    private ?RouteMatch $routeMatch = null;

    private function buildServices(array $services = [])
    {
        $this->serviceManager->setAllowOverride(true);

        foreach ($services as $service) {
            $this->services[$service] = \Mockery::mock($service);
            $this->serviceManager->setService($service, $this->services[$service]);
        }
    }

    private function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    private function getMockService(string $name): MockInterface
    {
        return $this->services[$name];
    }

    private function setRouter(RouteInterface $router): self
    {
        $this->router = $router;
        return $this;
    }

    private function destroyServices()
    {
        foreach ($this->services as $service) {
            $service = null;
        }

        $this->services = [];
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function dispatch(string $url, string $method = 'GET', array $body = [])
    {
        $uri = new Uri($url);
        $queryParams = [];
        parse_str($uri->getQuery(), $queryParams);
        $router = $this->serviceManager->get(RouteInterface::class);
        $request = (new ServerRequest())
            ->withQueryParams($queryParams)
            ->withMethod($method)
            ->withUri(new Uri($url))
            ->withParsedBody($body)
            ;

        $this->routeMatch = $router->match($request);
        $request = $request->withAttribute('matched_route_name', $this->routeMatch->getMatchedRouteName());
        $this->controller = $this->serviceManager->get($this->routeMatch->getParam('controller'));
        foreach ($this->routeMatch->getParams() as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        $this->response = $this->controller->handle($request);
    }

    public function assertControllerName(string $name)
    {
        $this->assertEquals($name, $this->routeMatch->getParam('controller'));
    }

    public function assertActionName(string $name)
    {
        $action = '';
        if (method_exists($this->controller, 'getActionName')) {
            $action = $this->controller->getActionName();
        } else {
            $action = $this->routeMatch->getParam('action');
        }
        $this->assertEquals($name, $action);
    }

    public function assertResponseStatusCode(int $code)
    {
        $this->assertEquals($code, $this->response->getStatusCode());
    }

    public function assertResponseHeaderContains($name, $value)
    {
        $this->assertEquals(
            $value,
            count($this->response->getHeader($name)) ? $this->response->getHeader($name)[0]: null
        );
    }

    public function assertHasResponseHeader($name)
    {
        $this->assertTrue(
            count($this->response->getHeader($name)) > 0
        );
    }
}
