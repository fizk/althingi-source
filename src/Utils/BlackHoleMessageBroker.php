<?php

namespace Althingi\Utils;

use Althingi\Injector\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class BlackHoleMessageBroker implements MessageBrokerInterface, LoggerAwareInterface
{
    private ?LoggerInterface $logger = null;

    public function produce(string $channel = null, string $topic = null, $message)
    {
        if ($this->logger) {
            $this->logger->debug("{$channel}:{$topic}", is_array($message) ? $message : [$message]);
        }
    }

    public function setLogger(LoggerInterface $logger): static
    {
        $this->logger = $logger;
        return $this;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
