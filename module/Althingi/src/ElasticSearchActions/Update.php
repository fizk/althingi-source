<?php

namespace Althingi\ElasticSearchActions;

use Elasticsearch\Client;
use Psr\Log\LoggerInterface;

class Update
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
        /** @var  $target \Althingi\ServiceEvents\UpdateEvent */
        $target = $event->getTarget();

        $presenter = $target->getPresenter();
        try {
            $this->client->index([
                'index' => $presenter->getIndex(),
                'type' => $presenter->getType(),
                'id' => $presenter->getIdentifier(),
                'body' => $presenter->getData(),
            ]);
            $this->logger->debug("Indexing [{$presenter->getIndex()} : {$presenter->getIdentifier()}]");
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
