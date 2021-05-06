<?php

namespace Althingi\Events;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestSuccessEvent
{
    private ServerRequestInterface $request;
    private ResponseInterface $response;

    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function __toString()
    {
        return implode(' ', [
            $this->request->getMethod(),
            $this->request->getUri()->__toString(),
            $this->response->getStatusCode()
        ]);
    }
}
