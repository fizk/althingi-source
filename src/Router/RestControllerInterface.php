<?php

namespace Althingi\Router;

use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;

interface RestControllerInterface
{
    public function post(ServerRequest $request): ResponseInterface;

    public function postList(ServerRequest $request): ResponseInterface;

    public function delete(ServerRequest $request): ResponseInterface;

    public function deleteList(ServerRequest $request): ResponseInterface;

    public function get(ServerRequest $request): ResponseInterface;

    public function getList(ServerRequest $request): ResponseInterface;

    public function head(ServerRequest $request): ResponseInterface;

    public function options(ServerRequest $request): ResponseInterface;

    public function optionsList(ServerRequest $request): ResponseInterface;

    public function patch(ServerRequest $request): ResponseInterface;

    public function patchList(ServerRequest $request): ResponseInterface;

    public function putList(ServerRequest $request): ResponseInterface;

    public function put(ServerRequest $request): ResponseInterface;
}
