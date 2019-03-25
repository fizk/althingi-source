<?php

namespace Althingi\Injector;

use Elasticsearch\Client;

interface ElasticSearchClientAwareInterface
{
    /**
     * @param \Elasticsearch\Client $client
     */
    public function setElasticSearchClient(Client $client);
}
