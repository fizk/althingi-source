<?php

namespace Althingi\QueueActions;

use Psr\Log\LoggerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Delete
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \PhpAmqpLib\Connection\AMQPStreamConnection */
    private $client;

    /** @var bool */
    private $forced;

    public function __construct(AMQPStreamConnection $client, LoggerInterface $logger, bool $isForced = false)
    {
        $this->logger = $logger;
        $this->client = $client;
        $this->forced = $isForced;
    }

    /**
     * @param \Laminas\EventManager\Event $event
     */
    public function __invoke(\Laminas\EventManager\Event $event)
    {
        /** @var  $target \Althingi\Events\UpdateEvent */
        $target = $event->getTarget();
        $params = $event->getParams();

        if ($params['rows'] > 0 || $this->forced === true) {
            $presenter = $target->getPresenter();
            $channel = $this->client->channel(1);

            $msg = new AMQPMessage(json_encode([
                'id' => $presenter->getIdentifier(),
                'body' => $presenter->getData(),
                'index' => $presenter->getIndex(),
            ]));

            $channel->basic_publish($msg, 'service', "{$presenter->getType()}.remove");
            $this->logger->info('QUEUE', [
                'service',
                "{$presenter->getType()}.remove",
                $presenter->getIdentifier()
            ]);
        }
    }
}
