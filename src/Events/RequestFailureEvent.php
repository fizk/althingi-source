<?php

namespace Althingi\Events;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class RequestFailureEvent
{
    private ServerRequestInterface $request;
    private Throwable $exception;

    public function __construct(ServerRequestInterface $request, Throwable $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    public function __toString()
    {
        return $this->request->getMethod() . $this->exception->getMessage();
    }
}
