<?php

namespace Althingi\Injector;

use Elasticsearch\Client;

interface ElasticSearchAwareInterface
{
    public function setElasticSearchClient(Client $client);

    public function getElasticSearchClient();
}
