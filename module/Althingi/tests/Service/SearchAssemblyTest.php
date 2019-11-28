<?php
namespace AlthingiTest\Service;

use Althingi\Model\Speech;
use Althingi\Service\SearchAssembly;
use Althingi\Service\SearchSpeech;
use Elasticsearch\ClientBuilder;
use GuzzleHttp\Ring\Client\MockHandler;
use PHPUnit\Framework\TestCase;

class SearchAssemblyTest extends TestCase
{
    /** @var  \Althingi\Service\SearchAssembly */
    private $service;

    public function setUp()
    {
        $handler = new MockHandler([
            'status' => 200,
            'transfer_stats' => [
                'total_time' => 100
            ],
            'body' => fopen('data://text/plain,' . json_encode($this->getJSON()), 'r'),
        ]);
        $builder = ClientBuilder::create();
        $builder->setHosts(['somehost']);
        $builder->setHandler($handler);
        $client = $builder->build();

        $this->service = new SearchAssembly();
        $this->service->setElasticSearchClient($client);
    }

    public function tearDown()
    {
        $this->service = null;
    }

    public function testTrue()
    {
        $this->assertTrue(true);
    }

//    public function testFetchAll()
//    {
//        $actual = $this->service->fetchAll('*', 1);
//
//        $this->assertInstanceOf('Althingi\Model\Issue', $actual[0]);
//        $this->assertInstanceOf('Althingi\Model\Speech', $actual[1]);
//    }

    private function getJSON()
    {
        return [
            '_shards' => [
                'failed' => 0,
                'skipped' => 0,
                'successful' => 2,
                'total' => 2
            ],
            'hits' => [
                'hits' => [
                    [
                        '_id' => '145-1-A',
                        '_index' => 'althingi_model_issue',
                        '_score' => 2.0,
                        '_source' => [
                            'additional_information' => '',
                            'assembly_id' => 145,
                            'category' => 'A',
                            'changes_in_law' => '',
                            'congressman_id' => null,
                            'costs_and_revenues' => '',
                            'deliveries' => null,
                            'goal' => '',
                            'issue_id' => 1,
                            'major_changes' => '',
                            'name' => 'fjárlög 2016',
                            'question' => null,
                            'status' => 'Samþykkt sem lög frá Alþingi',
                            'sub_name' => null,
                            'type' => 'l',
                            'type_name' => 'Frumvarp til laga',
                            'type_subname' => 'lagafrumvarp'
                        ],
                        '_type' => 'althingi_model_issue',
                        'highlight' => [
                            'name' => [],
                            'sub_name' => [],
                            'goal' => [],
                        ],
                    ],
                    [
                        '_id' => '20160602T003339',
                        '_index' => 'althingi_model_speech',
                        '_score' => 2.0,
                        '_source' => [
                            'assembly_id' => 145,
                            'category' => 'A',
                            'congressman_id' => 703,
                            'congressman_type' => 'félags- og húsnæðismálaráðherra',
                            'from' => '2016-06-02 00:33:39',
                            'issue_id' => 765,
                            'iteration' => null,
                            'plenary_id' => 123,
                            'speech_id' => '20160602T003339',
                            'text' => '',
                            'to' => '2016-06-02 00:35:04',
                            'type' => 'andsvar',
                            'word_count' => 245
                        ],
                        '_type' => 'althingi_model_speech',
                        'highlight' => [
                            'text' => []
                        ],
                    ]
                ],
                'max_score' => 2.0,
                'total' => [
                    'relation' => 'eq',
                    'value' => 2
                ]
            ],
            'timed_out' => false,
            'took' => 6
        ];
    }
}
