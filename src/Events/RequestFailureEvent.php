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

    public function toJSON()
    {
        return [
            'section_name' => 'request',
            'request_method' => count($this->request->getHeader('X-HTTP-Method-Override') ?? []) > 0
                ? $this->request->getHeader('X-HTTP-Method-Override')[0]
                : $this->request->getMethod(),
            'request_headers' => $this->request->getHeaders(),
            'request_uri' => $this->request->getUri()->__toString(),
            'response_status' => 500,
            'response_headers' => [],
            'error_file' => "{$this->exception->getFile()}:{$this->exception->getLine()}",
            'error_message' => $this->exception->getMessage(),
            'error_trace' => $this->exception->getTrace(),
        ];
    }

    public function __toString()
    {
        return json_encode($this->toJSON());
    }
}
