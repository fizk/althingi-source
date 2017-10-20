<?php

namespace Althingi\ElasticSearchActions;

use Althingi\ServiceEvents\ModelAndHydrator;
use Elasticsearch\Client;

class Add
{
    /** @var  \Elasticsearch\Client; */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param ModelAndHydrator $event
     */
    public function __invoke(ModelAndHydrator $event)
    {
        print_r($event->getHydrator());
        print_r(get_class($event->getModel()));
    }
}
