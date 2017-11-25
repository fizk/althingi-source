<?php

namespace Althingi\ElasticSearchActions;

use Althingi\Presenters\IndexablePresenterAwareInterface;
use Elasticsearch\Client;

class Update
{
    /** @var  \Elasticsearch\Client; */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param IndexablePresenterAwareInterface $event
     */
    public function __invoke(IndexablePresenterAwareInterface $event)
    {
        $presenter = $event->getPresenter();
        $this->client->index([
            'index' => $presenter->getIndex(),
            'type' => $presenter->getType(),
            'id' => $presenter->getIdentifier(),
            'body' => $presenter->getData(),
        ]);
    }
}
