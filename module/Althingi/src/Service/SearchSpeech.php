<?php

namespace Althingi\Service;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Injector\ElasticSearchAwareInterface;
use Althingi\Presenters\IndexableSpeechPresenter;
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
                    [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => [
                                        'issue_id' => [
                                            'value' => $issueId
                                        ]
                                    ]
                                ], [
                                    'term' => [
                                        'assembly_id' => [
                                            'value' => $assemblyId
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'query_string' => [
                            'default_operator' => 'OR',
                            'fields' => [
                                'text'
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
     * @return \Althingi\Model\Speech[]
     */
    public function fetchByAssembly(string $query, int $assemblyId): array
    {
        return $this->search([
            'bool' => [
                'must' => [
                    [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => [
                                        'assembly_id' => [
                                            'value' => $assemblyId
                                        ]
                                    ]
                                ],
                            ]
                        ]
                    ],
                    [
                        'query_string' => [
                            'default_operator' => 'OR',
                            'fields' => [
                                'text'
                            ],
                            'query' => $query
                        ]
                    ]
                ]
            ]
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
            'body' => [
                "from" => 0,
                "size" => 10,
                'query' => $query,
                'highlight' => [
                    'pre_tags' => ['<strong>'],
                    'post_tags' => ['</strong>'],
                    'fields' => [
                        'text' => new \stdClass()
                    ],
                    'require_field_match' => false
                ],
            ]
        ]);

        return array_map(function ($object) {
            $object['_source']['text'] = '<mgr>' . implode(' [...] ', array_map(function ($paragraph) {
                return strip_tags($paragraph, '<strong>');
            }, $object['highlight']['text'])) . '</mgr>';
            return (new Hydrator\Speech())->hydrate($object['_source'], new Model\Speech());
        }, $results['hits']['hits']);
    }
}
