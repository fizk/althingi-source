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

    public function deleteByQuery($params = array())
    {
        return [];
    }

    public function count($params = array())
    {
        return [];
    }

    public function countPercolate($params = array())
    {
        return [];
    }

    public function percolate($params)
    {
        return [];
    }

    public function mpercolate($params = array())
    {
        return [];
    }

    public function termvectors($params = array())
    {
        return [];
    }

    public function mtermvectors($params = array())
    {
        return [];
    }

    public function exists($params)
    {
        return false;
    }

    public function mget($params = array())
    {
        return [];
    }

    public function msearch($params = array())
    {
        return [];
    }

    public function msearchTemplate($params = array())
    {
        return [];
    }

    public function create($params)
    {
        return [];
    }

    public function bulk($params = array())
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

    public function suggest($params = array())
    {
        return [];
    }

    public function explain($params)
    {
        return [];
    }

    public function search($params = array())
    {
        return [
            'hits' => [
                'hits' => []
            ]
        ];
    }

    public function searchShards($params = array())
    {
        return [];
    }

    public function searchTemplate($params = array())
    {
        return [];
    }

    public function scroll($params = array())
    {
        return [];
    }

    public function clearScroll($params = array())
    {
        return [];
    }

    public function update($params)
    {
        return [];
    }

    public function updateByQuery($params = array())
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

    public function fieldStats($params = array())
    {
        return [];
    }

    public function fieldCaps($params = array())
    {
        return [];
    }

    public function renderSearchTemplate($params = array())
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
