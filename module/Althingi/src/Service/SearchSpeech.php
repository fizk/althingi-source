<?php

namespace Althingi\Service;

use Althingi\Lib\ElasticSearchAwareInterface;
use Althingi\Presenters\IndexableSpeechPresenter;
use Althingi\Hydrator\Speech as SpeechHydrator;
use Althingi\Model\Speech as SpeechModel;
use Elasticsearch\Client;

/**
 * Class Speech
 * @package Althingi\Service
 */
class SearchSpeech implements ElasticSearchAwareInterface
{
    /** @var  \Elasticsearch\Client */
    private $client;

    /**
     * @param string $query
     * @param int $assemblyId
     * @param int $issueId
     * @return \Althingi\Model\Speech[]
     */
    public function fetchByIssue(string $query, int $assemblyId, int $issueId): array
    {
        return $this->search([
            'bool' => [
                'must' => [
                    ['match' => ['text.raw' => $query]],
                    ['match' => ['assembly_id' => $assemblyId]],
                    ['match' => ['issue_id' => $issueId]],
                ],
            ],
        ]);
    }

    /**
     * @param string $query
     * @param int $assemblyId
     * @return \Althingi\Model\Speech[]
     */
    public function fetchByAssembly(string $query, int $assemblyId): array
    {
        return $this->search([
            'bool' => [
                'must' => [
                    ['fuzzy' => ['text.raw' => $query]],
                    ['match' => ['assembly_id' => $assemblyId]],
                ],
            ],
        ]);
    }

    /**
     * @param string $query
     * @return \Althingi\Model\Speech[]
     */
    public function fetch(string $query): array
    {
        return $this->search([
            'bool' => [
                'must' => [
                    ['fuzzy' => ['text.raw' => $query]],
                ],
            ],
        ]);
    }

    /**
     * @param Client $client
     * @return $this
     */
    public function setElasticSearchClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    public function getElasticSearchClient()
    {
        return $this->client;
    }

    /**
     * @param array $query
     * @return \Althingi\Model\Speech[]
     */
    private function search(array $query): array
    {
        $results = $this->client->search([
            'index' => IndexableSpeechPresenter::INDEX,
            'type' => IndexableSpeechPresenter::TYPE,
            'body' => [
                "from" => 0,
                "size" => 10,
                'query' => $query,
                'highlight' => [
                    'pre_tags' => ['<strong>'],
                    'post_tags' => ['</strong>'],
                    'fields' => [
                        'text.raw' => new \stdClass()
                    ],
                    'require_field_match' => false
                ],
            ]
        ]);

        return array_map(function ($object) {
            $object['_source']['text'] = '<mgr>' . implode(' [...] ', array_map(function ($paragraph) {
                return strip_tags($paragraph, '<strong>');
            }, $object['highlight']['text.raw'])) . '</mgr>';
            return (new SpeechHydrator())->hydrate($object['_source'], new SpeechModel());
        }, $results['hits']['hits']);
    }
}