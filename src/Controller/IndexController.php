<?php

namespace Althingi\Controller;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Server\RequestHandlerInterface;
use Althingi\Utils\OpenAPI;

class IndexController implements RequestHandlerInterface
{
    private OpenAPI $openApi;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routes = $this->openApi->transform(
            (new \Althingi\Utils\RouteInspector())
                ->run(require realpath(__DIR__ . '/../../config/route.php'))
        );

        return (new JsonResponse($routes));
    }

    public function setOpenApi(OpenAPI $openAPI)
    {
        $this->openApi = $openAPI;
        return $this;
    }
}
