<?php

namespace AlthingiTest;

use \Elasticsearch\Client as ElasticsearchClient;

class ElasticBlackHoleClient extends ElasticsearchClient
{

    public function __construct()
    {
    }

    public function info($params = [])
    {
        return [];
    }

    public function ping($params = [])
    {
        return false;
    }

    public function get($params)
    {
        return [];
    }

    public function getSource($params)
    {
        return [];
    }

    public function delete($params)
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

    public function exists($params)
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

    public function create($params)
    {
        return [];
    }

    public function bulk($params = [])
    {
        return [];
    }

    public function index($params)
    {
        return [];
    }

    public function reindex($params)
    {
        return [];
    }

    public function suggest($params = [])
    {
        return [];
    }

    public function explain($params)
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

    public function update($params)
    {
        return [];
    }

    public function updateByQuery($params = [])
    {
        return [];
    }

    public function getScript($params)
    {
        return [];
    }

    public function deleteScript($params)
    {
        return [];
    }

    public function putScript($params)
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

    public function indices()
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
