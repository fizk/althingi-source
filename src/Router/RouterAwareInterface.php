<?php

namespace Althingi\Router;

use Althingi\Router\RouteInterface;

interface RouterAwareInterface
{
    public function setRouter(RouteInterface $router): self;
}
