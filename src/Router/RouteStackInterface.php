<?php

namespace Althingi\Router;

interface RouteStackInterface extends RouteInterface
{
    public function addRoute(string $name, /*mixed*/ $route, int $priority = null): static;

    public function addRoutes(/*array|\Traversable*/$routes): static;

    public function removeRoute(string $name): static;

    public function setRoutes(/*array|\Traversable*/$routes): static;
}
