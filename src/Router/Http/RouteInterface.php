<?php

namespace Althingi\Router\Http;

use Althingi\Router\RouteInterface as BaseRoute;

/**
 * Tree specific route interface.
 */
interface RouteInterface extends BaseRoute
{
    /**
     * Get a list of parameters used while assembling.
     *
     * @return array
     */
    public function getAssembledParams();
}
