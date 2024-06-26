<?php

namespace Althingi\Router;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait RestControllerTrait
{
    protected string $identifierName = 'id';
    protected RouteMatch $routeMatch;
    protected ?string $action = null;

    public function setIdentifierName(string $name): self
    {
        $this->identifierName = (string) $name;
        return $this;
    }

    public function getIdentifierName(): string
    {
        return $this->identifierName;
    }

    protected function getIdentifier(ServerRequestInterface $request) /*int|string|bool*/
    {
        if ($identifierName = $request->getAttribute('identifier', false)) {
            $this->setIdentifierName($identifierName);
        }

        $identifier = $this->getIdentifierName();
        $id = $request->getAttribute($identifier, false);
        if ($id !== false) {
            return $id;
        }

        return false;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        // $action  = $this->routeMatch->getParam('action', false);
        $this->action  = $request->getAttribute('action', false);
        if ($this->action) {
            // Handle arbitrary methods, ending in Action
            $method = static::getMethodFromAction($this->action);
            if (!method_exists($this, $method)) {
                $method = 'notFoundAction';
            }
            $response = $this->$method($request);
            return $response;
        }

        // RESTful methods
        $method = $this->resolveMethod($request);
        switch ($method) {
            case 'delete':
                $id = $this->getIdentifier($request);

                if ($id !== false) {
                    $this->action = 'delete';
                    $response = $this->delete($request);
                    break;
                }

                $this->action = 'deleteList';
                $request->getAttribute('action', 'deleteList');
                $response = $this->deleteList($request);
                break;
                // GET
            case 'get':
                $id = $this->getIdentifier($request);
                if ($id !== false) {
                    $this->action = 'get';
                    $response = $this->get($request);
                    break;
                }
                $this->action = 'getList';
                $response = $this->getList($request);
                break;
                // HEAD
            case 'head':
                $id = $this->getIdentifier($request);
                if ($id === false) {
                    $id = null;
                }
                $this->action = 'head';
                $response = $this->head($request);
                break;
                // OPTIONS
            case 'options':
                $id = $this->getIdentifier($request);
                if ($id !== false) {
                    $this->action = 'options';
                    $response = $this->options($request);
                    break;
                }
                $this->action = 'optionsList';
                $response = $this->optionsList($request);
                break;
                // PATCH
            case 'patch':
                $id = $this->getIdentifier($request);

                if ($id !== false) {
                    $this->action = 'patch';
                    $response = $this->patch($request);
                    break;
                }

                $this->action = 'patchList';
                $response = $this->patchList($request);
                break;
                // POST
            case 'post':
                $id = $this->getIdentifier($request);

                if ($id == false) {
                    $this->action = 'post';
                    $response = $this->post($request);
                    break;
                }

                $this->action = 'postList';
                $response = $this->postList($request);
                break;
                // PUT
            case 'put':
                $id   = $this->getIdentifier($request);

                if ($id !== false) {
                    $this->action = 'put';
                    $response = $this->put($request);
                    break;
                }

                $this->action = 'putList';
                $response = $this->putList($request);
                break;
                // All others...
            default:
                $response = new EmptyResponse(405);
                break;
        }

        // $this->routeMatch->setParam('action', $action);
        // $e->setResult($response);
        return $response;
    }

    public function setRouteMatch(RouteMatch $routeMatch): self
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }

    public function url()
    {
        return new class ($this->router)
        {
            private SimpleRouteStack $r;
            public function __construct(SimpleRouteStack $router)
            {
                $this->r = $router;
            }
            public function fromRoute($url, $params)
            {
                return $this->r->assemble($params, ['name' => $url]);
            }
        };
    }

    public static function getMethodFromAction(string $action): string
    {
        $method  = str_replace(['.', '-', '_'], ' ', $action);
        $method  = ucwords($method);
        $method  = str_replace(' ', '', $method);
        $method  = lcfirst($method);
        $method .= 'Action';

        return $method;
    }

    public function post(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function postList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function delete(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function deleteList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function get(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function getList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function head(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function options(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function optionsList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function patch(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function patchList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function putList(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    public function put(ServerRequest $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

    private function resolveMethod(ServerRequest $request): string
    {
        if ($request->hasHeader('X-Http-Method-Override')) {
            return strtolower($request->getHeader('X-Http-Method-Override')[0]);
        }
        return strtolower($request->getMethod());
    }

    public function getActionName()
    {
        return $this->action;
    }
}
