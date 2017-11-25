<?php

namespace Althingi\Lib;

use Elasticsearch\Client;

interface ElasticSearchClientAwareInterface
{
    /**
     * @param \Elasticsearch\Client $client
     */
    public function setElasticSearchClient(Client $client);
}
