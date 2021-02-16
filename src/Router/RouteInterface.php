<?php

namespace Althingi\Router;

use Psr\Http\Message\RequestInterface;

/**
 * RouteInterface interface.
 */
interface RouteInterface
{
    public static function factory(/*array|Traversable*/$options = []): self;

    public function match(RequestInterface $request, $pathOffset = null, array $options = []);

    public function assemble(array $params = [], array $options = []);
}
