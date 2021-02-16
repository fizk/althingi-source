<?php

namespace Althingi\Router;

class RouteMatch
{
    protected array $params = [];
    protected ?string $matchedRouteName = null;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function setMatchedRouteName(?string $name): self
    {
        $this->matchedRouteName = $name;
        return $this;
    }

    public function getMatchedRouteName(): ?string
    {
        return $this->matchedRouteName;
    }

    public function setParam(string $name, string $value): self
    {
        $this->params[$name] = $value;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name, /*mixed*/ $default = null)/* : mixed*/
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        return $default;
    }
}
