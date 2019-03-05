<?php

namespace Althingi\QueueActions;

use Psr\Log\LoggerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Delete
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \PhpAmqpLib\Connection\AMQPStreamConnection */
    private $client;

    public function __construct(AMQPStreamConnection $client, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->client = $client;
    }

    /**
     * @param \Zend\EventManager\Event $event
     */
    public function __invoke(\Zend\EventManager\Event $event)
    {
        $this->logger->info(print_r($event, true));
    }
}
