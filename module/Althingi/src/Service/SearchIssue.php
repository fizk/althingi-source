<?php

namespace Althingi\Service;

use Althingi\Hydrator;
use Althingi\Model;
use Althingi\Injector\ElasticSearchAwareInterface;
use Althingi\Presenters\IndexableIssuePresenter;
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
     * @return \Althingi\Model\IssueAndDate[]
     */
    public function fetchByAssembly(string $query, int $assemblyId): array
    {
        return $this->search([
            'bool' => [
                'should' => [
                    ['term' => ['name.raw' => ['value' => $query, 'boost' => 1.0],]],
                    ['term' => ['sub_name.raw' => ['value' => $query, 'boost' => 1.0],]],
                    ['fuzzy' => ['goal.raw' => $query]],
                    ['fuzzy' => ['major_changes.raw' => $query]],
                    ['fuzzy' => ['changes_in_law.raw' => $query]],
                    ['fuzzy' => ['costs_and_revenues.raw' => $query]],
                    ['fuzzy' => ['deliveries.raw' => $query]],
                    ['fuzzy' => ['additional_information.raw' => $query]],
                ],
                'minimum_should_match' => 1,
                'must' => [
                    ['match' => ['assembly_id' => $assemblyId]],
                ],
            ],
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
            'index' => IndexableIssuePresenter::INDEX,
            'type' => IndexableIssuePresenter::TYPE,
            'body' => [
                "from" => 0,
                "size" => 10,
                'query' => $query,
                'highlight' => [
                    'pre_tags' => ['<strong>'],
                    'post_tags' => ['</strong>'],
                    'fields' => [
                        'goal.raw' => new \stdClass(),
                        'major_changes.raw' => new \stdClass(),
                        'changes_in_law.raw' => new \stdClass(),
                        'costs_and_revenues.raw' => new \stdClass(),
                        'deliveries.raw' => new \stdClass(),
                        'additional_information.raw' => new \stdClass(),
                    ],
                    'require_field_match' => false
                ],
            ]
        ]);

        return array_map(function ($object) {
            $object['_source']['goal'] = $this->stringifyHighlight($object, 'goal.raw');
            $object['_source']['major_changes'] = $this->stringifyHighlight($object, 'major_changes.raw');
            $object['_source']['changes_in_law'] = $this->stringifyHighlight($object, 'changes_in_law.raw');
            $object['_source']['costs_and_revenues'] = $this->stringifyHighlight($object, 'costs_and_revenues.raw');
            $object['_source']['deliveries'] = $this->stringifyHighlight($object, 'deliveries.raw');
            $object['_source']['additional_information'] =
                $this->stringifyHighlight($object, 'additional_information.raw');

            return (new Hydrator\Issue())->hydrate($object['_source'], new Model\Issue());
        }, $results['hits']['hits']);
    }

    private function stringifyHighlight($highlights, $key)
    {
        if (! array_key_exists('highlight', $highlights)) {
            return '';
        }

        if (array_key_exists($key, $highlights['highlight'])) {
            return '<mgr>' . implode(' [...] ', array_map(function ($paragraph) {
                return strip_tags($paragraph, '<strong>');
            }, $highlights['highlight'][$key])) . '</mgr>';
        } else {
            return '';
        }
    }
}
