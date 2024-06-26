<?php

namespace Althingi\Router\Http;

use Althingi\Router\RouteMatch as BaseRouteMatch;

class RouteMatch extends BaseRouteMatch
{
    protected int $length = 0;

    public function __construct(array $params, int $length = 0)
    {
        parent::__construct($params);

        $this->length = $length;
    }

    public function setMatchedRouteName(?string $name): static
    {
        if ($this->matchedRouteName === null) {
            $this->matchedRouteName = $name;
        } else {
            $this->matchedRouteName = $name . '/' . $this->matchedRouteName;
        }

        return $this;
    }

    public function merge(RouteMatch $match): static
    {
        $this->params  = array_merge($this->params, $match->getParams());
        $this->length += $match->getLength();

        $this->matchedRouteName = $match->getMatchedRouteName();

        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }
}
