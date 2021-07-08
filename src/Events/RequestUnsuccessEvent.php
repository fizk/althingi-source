<?php

namespace Althingi\Events;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};

class RequestUnsuccessEvent
{
    private ServerRequestInterface $request;
    private ResponseInterface $response;

    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
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
            'response_status' => $this->response->getStatusCode(),
            'response_headers' => $this->response->getHeaders(),
            'error_file' => null,
            'error_message' => "{$this->request->getUri()->__toString()}:{$this->response->getStatusCode()}",
            'error_trace' => json_decode($this->response->getBody()->__toString()),
        ];
    }

    public function __toString()
    {
        return json_encode($this->toJSON());
    }
}
