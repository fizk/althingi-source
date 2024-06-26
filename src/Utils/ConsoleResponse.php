<?php

namespace Althingi\Utils;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Laminas\Diactoros\StreamFactory;

class ConsoleResponse implements ResponseInterface
{
    private $message = '';

    public function __construct($message)
    {
        $this->message = $message;
    }
    public function getStatusCode(): int
    {
        return 200;
    }
    public function withStatus($code, $reasonPhrase = ''): static
    {
        return $this;
    }
    public function getReasonPhrase(): string
    {
        return '';
    }
    public function getProtocolVersion(): string
    {
        return '1.1';
    }
    public function withProtocolVersion($version): static
    {
        return $this;
    }
    public function getHeaders(): array
    {
        return [];
    }
    public function hasHeader($name): bool
    {
        return false;
    }
    public function getHeader($name): array
    {
        return [];
    }
    public function getHeaderLine($name): string
    {
        return '';
    }
    public function withHeader($name, $value): static
    {
        return $this;
    }
    public function withAddedHeader($name, $value): static
    {
        return $this;
    }
    public function withoutHeader($name): static
    {
        return $this;
    }
    public function getBody(): StreamInterface
    {
        return (new StreamFactory())->createStream($this->message . "\n");
    }
    public function withBody(StreamInterface $body): static
    {
        return $this;
    }
}
