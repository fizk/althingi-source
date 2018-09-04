<?php
namespace AlthingiTest\Service;

use Althingi\Model\Speech;
use Althingi\Service\SearchSpeech;
use Elasticsearch\ClientBuilder;
use GuzzleHttp\Ring\Client\MockHandler;
use PHPUnit\Framework\TestCase;

class SearchSpeechTest extends TestCase
{
    /** @var  \Althingi\Service\SearchSpeech */
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

        $this->service = new SearchSpeech();
        $this->service->setElasticSearchClient($client);
    }

    public function tearDown()
    {
        $this->service = null;
    }

    public function testFetch()
    {
        $expected = [(new Speech())
            ->setSpeechId('00001-id')
            ->setPlenaryId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setIssueId(1)
            ->setCongressmanId(1)
            ->setText('<mgr>hani [...] krummi [...] hundur [...] svin</mgr>')];
        $actual = $this->service->fetch('some query');

        $this->assertEquals($expected, $actual);
    }

    public function fetchByIssue()
    {
        $expected = [(new Speech())
            ->setSpeechId('00001-id')
            ->setPlenaryId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setCongressmanId(1)
            ->setText('<mgr>hani [...] krummi [...] hundur [...] svin</mgr>')];
        $actual = $this->service->fetchByIssue(1, 1, 'some query');

        $this->assertEquals($expected, $actual);
    }

    public function fetchByAssembly()
    {
        $expected = [(new Speech())
            ->setSpeechId('00001-id')
            ->setPlenaryId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setCongressmanId(1)
            ->setText('<mgr>hani [...] krummi [...] hundur [...] svin</mgr>')];
        $actual = $this->service->fetchByAssembly(1, 'some query');

        $this->assertEquals($expected, $actual);
    }

    private function getJSON()
    {
        return [
            'hits' => [
                'total' => 1,
                'max_score' => 0.30685282,
                'hits' => [
                    [
                        '_index' => '',
                        '_type' => '',
                        '_id' => '00001-id',
                        '_score' => '',
                        '_source' =>
                            (new Speech())
                                ->setSpeechId('00001-id')
                                ->setPlenaryId(1)
                                ->setCategory('A')
                                ->setAssemblyId(1)
                                ->setIssueId(1)
                                ->setCongressmanId(1)
                                ->toArray(),
                        'highlight' => [
                            'text.raw' => [
                                'hani',
                                'krummi',
                                'hundur',
                                'svin',
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }
}
