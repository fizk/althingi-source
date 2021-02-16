<?php

namespace Althingi\Router;

interface RouteStackInterface extends RouteInterface
{
    public function addRoute(string $name, /*mixed*/ $route, int $priority = null): self;

    public function addRoutes(/*array|\Traversable*/$routes): self;

    public function removeRoute(string $name): self;

    public function setRoutes(/*array|\Traversable*/$routes): self;
}
