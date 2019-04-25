<?php

namespace Althingi\Service;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Injector\ElasticSearchAwareInterface;
use Althingi\Presenters\IndexableIssuePresenter;
use Althingi\Presenters\IndexableSpeechPresenter;
use Elasticsearch\Client;

/**
 * Class Speech
 * @package Althingi\Service
 */
class SearchIssue implements ElasticSearchAwareInterface
{
    /** @var  \Elasticsearch\Client */
    private $client;

    /**
     * @param string $query
     * @param int $assemblyId
     * @return \Althingi\Model\Issue[]
     */
    public function fetchAll(string $query, int $assemblyId): array
    {
        return $this->search([
            'bool' => [
                'must' => [
                    [
                        'term' => [
                            'assembly_id' => [
                                'value' => $assemblyId
                            ]
                        ]
                    ],
                    [
                        'query_string' => [
                            'default_operator' => 'OR',
                            'fields' => [
                                'name',
                                'sub_name',
                                'goal'
                            ],
                            'query' => $query
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param string $query
     * @param int $assemblyId
     * @return \Althingi\Model\Issue[]
     */
    public function fetchByAssembly(string $query, int $assemblyId): array
    {
        return $this->search([
            'bool' => [
                'must' => [
                    [
                        'term' => [
                            'assembly_id' => [
                                'value' => $assemblyId
                            ]
                        ]
                    ],
                    [
                        'query_string' => [
                            'default_operator' => 'OR',
                            'fields' => [
                                'name',
                                'sub_name',
                                'goal'
                            ],
                            'query' => $query
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * @param string $query
     * @return \Althingi\Model\IssueAndDate[]
     */
    public function fetch(string $query): array
    {
        return $this->search([
            'bool' => [
                'must' => [
                    ['match' => ['text.raw' => $query]],
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
     * @return \Althingi\Model\IssueAndDate[]
     */
    private function search(array $query): array
    {
        $results = $this->client->search([
            'index' => implode(', ', [
                IndexableIssuePresenter::INDEX,
            ]),
            'body' => [
                "from" => 0,
                "size" => 10,
                'query' => $query,
                'highlight' => [
                    'pre_tags' => ['<strong>'],
                    'post_tags' => ['</strong>'],
                    'fields' => [
                        'name' => new \stdClass(),
                        'goal' => new \stdClass(),
                        'major_changes' => new \stdClass(),
                        'changes_in_law' => new \stdClass(),
                        'costs_and_revenues' => new \stdClass(),
                        'deliveries' => new \stdClass(),
                        'additional_information' => new \stdClass(),
                    ],
                    'require_field_match' => false
                ],
            ]
        ]);

        return array_map(function ($object) {
            $object['_source']['name'] = $this->stringifyHighlight($object, 'name');
            $object['_source']['goal'] = $this->stringifyHighlight($object, 'goal');
            $object['_source']['major_changes'] = $this->stringifyHighlight($object, 'major_changes');
            $object['_source']['changes_in_law'] = $this->stringifyHighlight($object, 'changes_in_law');
            $object['_source']['costs_and_revenues'] = $this->stringifyHighlight($object, 'costs_and_revenues');
            $object['_source']['deliveries'] = $this->stringifyHighlight($object, 'deliveries');
            $object['_source']['additional_information'] =
                $this->stringifyHighlight($object, 'additional_information');

            return (new Hydrator\Issue())->hydrate($object['_source'], new Model\Issue());
        }, $results['hits']['hits']);
    }

    private function stringifyHighlight($highlights, $key)
    {
        if (! array_key_exists('highlight', $highlights)) {
            return $highlights['_source'][$key];
        }

        if (array_key_exists($key, $highlights['highlight'])) {
            return '<mgr>' . implode(' [...] ', array_map(function ($paragraph) {
                return strip_tags($paragraph, '<strong>');
            }, $highlights['highlight'][$key])) . '</mgr>';
        } else {
            return $highlights['_source'][$key];
        }
    }
}
