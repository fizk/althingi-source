<?php

namespace Althingi\ElasticSearchActions;

use Elasticsearch\Client;
use Psr\Log\LoggerInterface;

class Delete
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
    public function __invoke(\Zend\EventManager\Event $event)
    {
        /** @var  $target \Althingi\Events\DeleteEvent */
        $target = $event->getTarget();
        try {
            $presenter = $target->getPresenter();
            $this->client->delete([
                'index' => $presenter->getIndex(),
                'type' => $presenter->getType(),
                'id' => $presenter->getIdentifier(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
