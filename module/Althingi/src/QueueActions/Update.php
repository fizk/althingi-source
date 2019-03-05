<?php

namespace Althingi\QueueActions;

use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Update
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
    public function __invoke(\Zend\EventManager\Event $event): void
    {
        /** @var  $target \Althingi\Events\UpdateEvent */
        $target = $event->getTarget();

        $presenter = $target->getPresenter();
        $channel = $this->client->channel();

        $msg = new AMQPMessage(json_encode([
            'id' => $presenter->getIdentifier(),
            'body' => $presenter->getData(),
        ]));

        $channel->basic_publish($msg, 'service', "{$presenter->getType()}.update");

        $this->logger->info(print_r($event, true));
    }
}
