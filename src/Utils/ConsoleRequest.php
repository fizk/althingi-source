<?php

namespace Althingi\Utils;

use Laminas\Diactoros\Stream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ConsoleRequest implements ServerRequestInterface
{
    private UriInterface $uri;
    private array $attributes = [];

    public function __construct(UriInterface $uri)
    {
        $this->uri = $uri;
    }

    public function getServerParams(): array
    {
        return [];
    }

    public function getCookieParams(): array
    {
        return [];
    }

    public function withCookieParams(array $cookies): static
    {
        return $this;
    }

    public function getQueryParams(): array
    {
        return [];
    }

    public function withQueryParams(array $query): static
    {
        return $this;
    }

    public function getUploadedFiles(): array
    {
        return [];
    }

    public function withUploadedFiles(array $uploadedFiles): static
    {
        return $this;
    }

    public function getParsedBody()
    {
        return null;
    }

    public function withParsedBody($data): static
    {
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute($attribute, $default = null)
    {
        if (!array_key_exists($attribute, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$attribute];
    }

    public function withAttribute($attribute, $value): static
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;
        return $new;
    }

    public function withoutAttribute($name): static
    {
        return $this;
    }

    public function getRequestTarget(): string
    {
        return '/';
    }

    public function withRequestTarget($requestTarget): static
    {
        return $this;
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function withMethod($method): static
    {
        return $this;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }
    public function withUri(UriInterface $uri, $preserveHost = false): static
    {
        $this->uri = $uri;
        return $this;
    }
    public function getProtocolVersion(): string
    {
        return '';
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
        return new Stream('');
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body): static
    {
        return $this;
    }
}
