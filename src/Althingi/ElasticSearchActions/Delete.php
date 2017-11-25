<?php

namespace Althingi\ElasticSearchActions;

use Althingi\Presenters\IndexablePresenterAwareInterface;
use Elasticsearch\Client;

class Delete
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
        $this->client->delete([
            'index' => $presenter->getIndex(),
            'type' => $presenter->getType(),
            'id' => $presenter->getIdentifier(),
        ]);
    }
}
