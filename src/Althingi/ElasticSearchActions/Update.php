<?php

namespace Althingi\ElasticSearchActions;

use Althingi\ServiceEvents\ModelAndHydrator;
use Elasticsearch\Client;

class Update
{
    /** @var  \Elasticsearch\Client; */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(ModelAndHydrator $event)
    {
        // TODO: Implement __invoke() method.
    }

}
