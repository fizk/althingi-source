<?php

namespace AlthingiTest;

use \Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\Namespaces\IndicesNamespace;

class ElasticBlackHoleClient extends ElasticsearchClient
{

    public function __construct()
    {
    }

    public function info($params = [])
    {
        return [];
    }

    public function ping(array $params = []): bool
    {
        return false;
    }

    public function get(array $params = [])
    {
        return [];
    }

    public function getSource(array $params = [])
    {
        return [];
    }

    public function delete(array $params = [])
    {
        return [];
    }

    public function deleteByQuery($params = [])
    {
        return [];
    }

    public function count($params = [])
    {
        return [];
    }

    public function countPercolate($params = [])
    {
        return [];
    }

    public function percolate($params)
    {
        return [];
    }

    public function mpercolate($params = [])
    {
        return [];
    }

    public function termvectors($params = [])
    {
        return [];
    }

    public function mtermvectors($params = [])
    {
        return [];
    }

    public function exists(array $params = []): bool
    {
        return false;
    }

    public function mget($params = [])
    {
        return [];
    }

    public function msearch($params = [])
    {
        return [];
    }

    public function msearchTemplate($params = [])
    {
        return [];
    }

    public function create(array $params = [])
    {
        return [];
    }

    public function bulk($params = [])
    {
        return [];
    }

    public function index(array $params = [])
    {
        return [];
    }

    public function reindex(array $params = [])
    {
        return [];
    }

    public function suggest($params = [])
    {
        return [];
    }

    public function explain(array $params = [])
    {
        return [];
    }

    public function search($params = [])
    {
        return [
            'hits' => [
                'hits' => []
            ]
        ];
    }

    public function searchShards($params = [])
    {
        return [];
    }

    public function searchTemplate($params = [])
    {
        return [];
    }

    public function scroll($params = [])
    {
        return [];
    }

    public function clearScroll($params = [])
    {
        return [];
    }

    public function update(array $params = [])
    {
        return [];
    }

    public function updateByQuery(array $params = [])
    {
        return [];
    }

    public function getScript(array $params = [])
    {
        return [];
    }

    public function deleteScript(array $params = [])
    {
        return [];
    }

    public function putScript(array $params = [])
    {
        return [];
    }

    public function getTemplate($params)
    {
        return [];
    }

    public function deleteTemplate($params)
    {
        return [];
    }

    public function putTemplate($params)
    {
        return [];
    }

    public function fieldStats($params = [])
    {
        return [];
    }

    public function fieldCaps($params = [])
    {
        return [];
    }

    public function renderSearchTemplate($params = [])
    {
        return [];
    }

    public function indices(): IndicesNamespace
    {
        return null;
    }

    public function cluster()
    {
        return null;
    }

    public function nodes()
    {
        return null;
    }

    public function snapshot()
    {
        return null;
    }

    public function cat()
    {
        return null;
    }

    public function ingest()
    {
        return null;
    }

    public function tasks()
    {
        return null;
    }

    public function remote()
    {
        return null;
    }

    public function __call($name, $arguments)
    {
        return null;
    }

    public function extractArgument(&$params, $arg)
    {
        return null;
    }
}
