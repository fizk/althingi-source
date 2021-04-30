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
    public function getStatusCode()
    {
        return 200;
    }
    public function withStatus($code, $reasonPhrase = '')
    {
        return $this;
    }
    public function getReasonPhrase()
    {
        return '';
    }
    public function getProtocolVersion()
    {
        return '1.1';
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
        return [];
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
        return (new StreamFactory())->createStream($this->message . "\n");
    }
    public function withBody(StreamInterface $body)
    {
        return $this;
    }
}
