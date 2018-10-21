<?php

namespace Althingi\ElasticSearchActions;

use Elasticsearch\Client;
use Psr\Log\LoggerInterface;

class Add
{
    /** @var  \Elasticsearch\Client; */
    private $client;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param \Zend\EventManager\Event $event
     */
    public function __invoke(\Zend\EventManager\Event $event): void
    {
        /** @var  $target \Althingi\ServiceEvents\AddEvent */
        $target = $event->getTarget();
        try {
            $presenter = $target->getPresenter();
            $this->client->index([
                'index' => $presenter->getIndex(),
                'type' => $presenter->getType(),
                'id' => $presenter->getIdentifier(),
                'body' => $presenter->getData(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
