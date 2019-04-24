<?php

namespace AlthingiTest\Service;

use Althingi\QueueActions\QueueEventsListener;
use Althingi\Service\Speech;
use Althingi\Utils\RabbitMQBlackHoleClient;
use AlthingiTest\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Speech as SpeechModel;
use Althingi\Model\SpeechAndPosition as SpeechAndPositionModel;
use Althingi\Model\DateAndCount as DateAndCountModel;
use Psr\Log\NullLogger;
use Zend\EventManager\EventManager;

class SpeechTest extends TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGetSpeech()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = (new SpeechModel())
            ->setSpeechId('id--00001')
            ->setAssemblyId(1)
            ->setPlenaryId(1)
            ->setCategory('A')
            ->setIssueId(1)
            ->setCongressmanId(1);

        $actualData = $service->get('id--00001');

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetSpeechNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = null;

        $actualData = $service->get('invalid-id');

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetch()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new SpeechAndPositionModel())
                ->setSpeechId('id--00003')
                ->setAssemblyId(1)
                ->setPlenaryId(1)
                ->setCategory('A')
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(2),

            (new SpeechAndPositionModel())
                ->setSpeechId('id--00004')
                ->setAssemblyId(1)
                ->setPlenaryId(1)
                ->setCategory('A')
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(3),
        ];

        $actualData = $service->fetch('id--00004', 1, 1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = [];

        $actualData = $service->fetch('id--invalid', 1, 1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new SpeechAndPositionModel())
                ->setSpeechId('id--00001')
                ->setAssemblyId(1)
                ->setPlenaryId(1)
                ->setCategory('A')
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(0),

            (new SpeechAndPositionModel())
                ->setSpeechId('id--00002')
                ->setAssemblyId(1)
                ->setPlenaryId(1)
                ->setCategory('A')
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(1),
        ];

        $actualData = $service->fetchByIssue(1, 1, 'A', 0, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCountByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = 4;
        $actualData = $service->countByIssue(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchFrequencyByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-01-01')),
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-02-01')),
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-03-01')),
        ];
        $actualData = $service->fetchFrequencyByIssue(1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchFrequencyByAssembly()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = [
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-01-01')),
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-02-01')),
            (new DateAndCountModel())->setCount(60)->setDate(new \DateTime('2000-03-01')),
        ];
        $actualData = $service->fetchFrequencyByAssembly(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCountTotalTimeByAssemblyAndCongressman()
    {
        $service = new Speech();
        $service->setDriver($this->pdo);

        $expectedData = 120;
        $actualData = $service->countTotalTimeByAssemblyAndCongressman(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $serviceEventListener = (new QueueEventsListener())
            ->setQueue(new RabbitMQBlackHoleClient())
            ->setLogger(new NullLogger())
            ->setIsForced(true);
        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $speech = (new SpeechModel())
            ->setSpeechId('id--20001')
            ->setPlenaryId(1)
            ->setAssemblyId(3)
            ->setIssueId(1)
            ->setCategory('A')
            ->setCongressmanId(1);

        $expectedTable = $this->createArrayDataSet([
            'Speech' => [
                [
                    'speech_id' => 'id--20001',
                    'plenary_id' => 1,
                    'assembly_id' => 3,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'congressman_type' => null,
                    'from' => null,
                    'to' => null,
                    'text' => null,
                    'type' => null,
                    'iteration' => null,
                    'word_count' => 0,
                    'validated' => 1,
                ]
            ],
        ])->getTable('Speech');
        $actualTable = $this->getConnection()
            ->createQueryTable('Speech', 'SELECT * FROM Speech where `assembly_id` = 3');

        $speechService = new Speech();
        $speechService->setDriver($this->pdo);
        $speechService->setEventManager($eventManager);
        $speechService->create($speech);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $serviceEventListener = (new QueueEventsListener())
            ->setQueue(new RabbitMQBlackHoleClient())
            ->setLogger(new NullLogger())
            ->setIsForced(true);
        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $speech = (new SpeechModel())
            ->setSpeechId('id--20001')
            ->setPlenaryId(1)
            ->setCategory('A')
            ->setAssemblyId(3)
            ->setIssueId(1)
            ->setCongressmanId(1);

        $expectedTable = $this->createArrayDataSet([
            'Speech' => [
                [
                    'speech_id' => 'id--20001',
                    'plenary_id' => 1,
                    'assembly_id' => 3,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'congressman_type' => null,
                    'from' => null,
                    'to' => null,
                    'text' => null,
                    'type' => null,
                    'iteration' => null,
                    'word_count' => 0,
                    'validated' => 1,
                ]
            ],
        ])->getTable('Speech');
        $actualTable = $this->getConnection()
            ->createQueryTable('Speech', 'SELECT * FROM Speech where `assembly_id` = 3');

        $speechService = new Speech();
        $speechService->setDriver($this->pdo);
        $speechService->setEventManager($eventManager);
        $speechService->save($speech);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $serviceEventListener = (new QueueEventsListener())
            ->setQueue(new RabbitMQBlackHoleClient())
            ->setLogger(new NullLogger())
            ->setIsForced(true);
        $eventManager = new EventManager();
        $serviceEventListener->attach($eventManager);

        $speech = (new SpeechModel())
            ->setSpeechId('id--00001')
            ->setPlenaryId(1)
            ->setAssemblyId(1)
            ->setCategory('A')
            ->setIssueId(1)
            ->setCongressmanId(2);

        $expectedTable = $this->createArrayDataSet([
            'Speech' => [
                [
                    'speech_id' => 'id--00001',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 2,
                    'congressman_type' => null,
                    'from' => null,
                    'to' => null,
                    'text' => null,
                    'type' => null,
                    'iteration' => null,
                    'word_count' => 0,
                    'validated' => 1,
                ]
            ],
        ])->getTable('Speech');
        $actualTable = $this->getConnection()
            ->createQueryTable('Speech', 'SELECT * FROM Speech where `speech_id` = "id--00001"');

        $speechService = new Speech();
        $speechService->setDriver($this->pdo);
        $speechService->setEventManager($eventManager);
        $speechService->update($speech);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
              ['assembly_id' => 1, 'from' => '2000-01-01'],
              ['assembly_id' => 2, 'from' => '2000-01-01'],
              ['assembly_id' => 3, 'from' => '2000-01-01'],
            ],
            'Issue' => [
                ['assembly_id' => 1, 'issue_id' => 1, 'category' => 'A',],
                ['assembly_id' => 1, 'issue_id' => 2, 'category' => 'A',],
                ['assembly_id' => 1, 'issue_id' => 3, 'category' => 'A',],
                ['assembly_id' => 2, 'issue_id' => 1, 'category' => 'A',],
                ['assembly_id' => 2, 'issue_id' => 2, 'category' => 'A',],
                ['assembly_id' => 2, 'issue_id' => 3, 'category' => 'A',],

                ['assembly_id' => 3, 'issue_id' => 1, 'category' => 'A',],
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'congressman 1', 'birth' => '2000-01-01'],
                ['congressman_id' => 2, 'name' => 'congressman 2', 'birth' => '2000-01-01'],
                ['congressman_id' => 3, 'name' => 'congressman 3', 'birth' => '2000-01-01'],
            ],
            'Plenary' => [
                ['plenary_id' => 1, 'assembly_id' => 1],
                ['plenary_id' => 2, 'assembly_id' => 1],
                ['plenary_id' => 3, 'assembly_id' => 1],
                ['plenary_id' => 1, 'assembly_id' => 3],
            ],
            'Speech' => [
                [
                    'speech_id' => 'id--00001',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                ],[
                    'speech_id' => 'id--00002',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                ],[
                    'speech_id' => 'id--00003',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                ],[
                    'speech_id' => 'id--00004',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                ],[
                    'speech_id' => 'id--10001',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                ],[
                    'speech_id' => 'id--10002',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2000-01-01 00:01:00',
                    'validated' => true,
                ],[
                    'speech_id' => 'id--10003',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => '2000-02-01 00:00:00',
                    'to' => '2000-02-01 00:01:00',
                    'validated' => true,
                ],[
                    'speech_id' => 'id--10004',
                    'plenary_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'category' => 'A',
                    'congressman_id' => 2,
                    'from' => '2000-03-01 00:00:00',
                    'to' => '2000-03-01 00:01:00',
                    'validated' => true,
                ]
            ],

        ]);
    }
}
