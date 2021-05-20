<?php

namespace Althingi\Utils;

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

    public function getServerParams()
    {
        return [];
    }

    public function getCookieParams()
    {
        return [];
    }

    public function withCookieParams(array $cookies)
    {
        return $this;
    }

    public function getQueryParams()
    {
        return [];
    }

    public function withQueryParams(array $query)
    {
        return $this;
    }

    public function getUploadedFiles()
    {
        return [];
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        return $this;
    }

    public function getParsedBody()
    {
        return null;
    }

    public function withParsedBody($data)
    {
        return $this;
    }

    public function getAttributes()
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

    public function withAttribute($attribute, $value)
    {
        $new = clone $this;
        $new->attributes[$attribute] = $value;
        return $new;
    }

    public function withoutAttribute($name)
    {
        return $this;
    }

    public function getRequestTarget()
    {
        return '/';
    }

    public function withRequestTarget($requestTarget)
    {
        return $this;
    }

    public function getMethod()
    {
        return 'GET';
    }

    public function withMethod($method)
    {
        return $this;
    }

    public function getUri()
    {
        return $this->uri;
    }
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $this->uri = $uri;
        return $this;
    }
    public function getProtocolVersion()
    {
        return '';
    }
    public function withProtocolVersion($version)
    {
        return $this;
    }
    public function getHeaders()
    {
        return [];
    }
    public function hasHeader($name)
    {
        return false;
    }
    public function getHeader($name)
    {
        return null;
    }
    public function getHeaderLine($name)
    {
        return '';
    }
    public function withHeader($name, $value)
    {
        return $this;
    }
    public function withAddedHeader($name, $value)
    {
        return $this;
    }
    public function withoutHeader($name)
    {
        return $this;
    }
    public function getBody()
    {
        return null;
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
    public function withBody(StreamInterface $body)
    {
        return $this;
    }
}
