<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Events\{UpdateEvent, AddEvent};
use Althingi\Model;
use Mockery;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class SpeechTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSpeech()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = (new Model\Speech())
            ->setSpeechId('id--00001')
            ->setAssemblyId(1)
            ->setParliamentarySessionId(1)
            ->setKind(Model\KindEnum::A)
            ->setIssueId(1)
            ->setCongressmanId(1);

        $actualData = $service->get('id--00001');

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getSpeechNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = null;

        $actualData = $service->get('invalid-id');

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchSuccess()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = [
            (new Model\SpeechAndPosition())
                ->setSpeechId('id--00003')
                ->setAssemblyId(1)
                ->setParliamentarySessionId(1)
                ->setKind(Model\KindEnum::A)
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(2),

            (new Model\SpeechAndPosition())
                ->setSpeechId('id--00004')
                ->setAssemblyId(1)
                ->setParliamentarySessionId(1)
                ->setKind(Model\KindEnum::A)
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(3),
        ];

        $actualData = $service->fetch('id--00004', 1, 1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchNotFound()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = [];

        $actualData = $service->fetch('id--invalid', 1, 1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = [
            (new Model\SpeechAndPosition())
                ->setSpeechId('id--00001')
                ->setAssemblyId(1)
                ->setParliamentarySessionId(1)
                ->setKind(Model\KindEnum::A)
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(0),

            (new Model\SpeechAndPosition())
                ->setSpeechId('id--00002')
                ->setAssemblyId(1)
                ->setParliamentarySessionId(1)
                ->setKind(Model\KindEnum::A)
                ->setIssueId(1)
                ->setCongressmanId(1)
                ->setPosition(1),
        ];

        $actualData = $service->fetchByIssue(1, 1, Model\KindEnum::A, 0, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function countByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = 4;
        $actualData = $service->countByIssue(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchFrequencyByIssue()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = [
            (new Model\DateAndCount())->setCount(60)->setDate(new \DateTime('2000-01-01')),
            (new Model\DateAndCount())->setCount(60)->setDate(new \DateTime('2000-02-01')),
            (new Model\DateAndCount())->setCount(60)->setDate(new \DateTime('2000-03-01')),
        ];
        $actualData = $service->fetchFrequencyByIssue(1, 2);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function fetchFrequencyByAssembly()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = [
            (new Model\DateAndCount())->setCount(60)->setDate(new \DateTime('2000-01-01')),
            (new Model\DateAndCount())->setCount(60)->setDate(new \DateTime('2000-02-01')),
            (new Model\DateAndCount())->setCount(60)->setDate(new \DateTime('2000-03-01')),
        ];
        $actualData = $service->fetchFrequencyByAssembly(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function countTotalTimeByAssemblyAndCongressman()
    {
        $service = new Speech();
        $service->setDriver($this->getPDO());

        $expectedData = 120;
        $actualData = $service->countTotalTimeByAssemblyAndCongressman(1, 1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function createSuccess()
    {
        $speech = (new Model\Speech())
            ->setSpeechId('id--20001')
            ->setParliamentarySessionId(1)
            ->setAssemblyId(3)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1);

        $expectedTable = $this->createArrayDataSet([
            'Speech' => [
                [
                    'speech_id' => 'id--20001',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 3,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
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
        $speechService->setDriver($this->getPDO());
        $speechService->create($speech);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $speech = (new Model\Speech())
            ->setSpeechId('id--20001')
            ->setParliamentarySessionId(1)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(3)
            ->setIssueId(1)
            ->setCongressmanId(1);

        $expectedTable = $this->createArrayDataSet([
            'Speech' => [
                [
                    'speech_id' => 'id--20001',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 3,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
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
        $speechService->setDriver($this->getPDO());
        $speechService->save($speech);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveParliamentarySessionDoesntExist()
    {
        $speech = (new Model\Speech())
            ->setSpeechId('id--20001')
            ->setParliamentarySessionId(10000)
            ->setKind(Model\KindEnum::A)
            ->setAssemblyId(3)
            ->setIssueId(1)
            ->setCongressmanId(1);

        $speechService = new Speech();
        $speechService->setDriver($this->getPDO());
        try {
            $speechService->save($speech);
        } catch (\PDOException $e) {
            $this->assertEquals(1452, $e->errorInfo[1]);
        }
    }

    #[Test]
    public function updateSuccess()
    {
        $speech = (new Model\Speech())
            ->setSpeechId('id--00001')
            ->setParliamentarySessionId(1)
            ->setAssemblyId(1)
            ->setKind(Model\KindEnum::A)
            ->setIssueId(1)
            ->setCongressmanId(2);

        $expectedTable = $this->createArrayDataSet([
            'Speech' => [
                [
                    'speech_id' => 'id--00001',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
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
        $speechService->setDriver($this->getPDO());
        $speechService->update($speech);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function createFireEventResourceCreated()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $speech = (new Model\Speech())
            ->setSpeechId('unique-id')
            ->setParliamentarySessionId(1)
            ->setAssemblyId(3)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1);

        (new Speech())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->create($speech)
        ;
    }

    #[Test]
    public function updateFireEventResourceFoundNoUpdate()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $speech = (new Model\Speech())
            ->setSpeechId('id--00001')
            ->setParliamentarySessionId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setValidated(true);

        (new Speech())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($speech)
        ;
    }

    #[Test]
    public function updateFireEventResourceFoundUpdateNeeded()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $speech = (new Model\Speech())
            ->setSpeechId('id--00001')
            ->setParliamentarySessionId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setValidated(false);

        (new Speech())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->update($speech)
        ;
    }

    #[Test]
    public function saveFireEventResourceCreated()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (AddEvent $args) {
                $this->assertEquals(1, $args->getParams()['rows']);
                return $args instanceof AddEvent;
            })
            ->getMock();

        $speech = (new Model\Speech())
            ->setSpeechId('unique-id')
            ->setParliamentarySessionId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setValidated(false);

        (new Speech())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($speech)
        ;
    }

    #[Test]
    public function saveFireEventResourceFoundNoUpdateNeeded()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(0, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $speech = (new Model\Speech())
            ->setSpeechId('id--00001')
            ->setParliamentarySessionId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setValidated(true);

        (new Speech())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($speech)
        ;
    }

    #[Test]
    public function saveFireEventResourceFoundUpdateRequired()
    {
        /** @var  \Psr\EventDispatcher\EventDispatcherInterface */
        $eventDispatcher = Mockery::mock(EventDispatcherInterface::class)
            ->shouldReceive('dispatch')
            ->once()
            ->withArgs(function (UpdateEvent $args) {
                $this->assertEquals(2, $args->getParams()['rows']);
                return $args instanceof UpdateEvent;
            })
            ->getMock();

        $speech = (new Model\Speech())
            ->setSpeechId('id--00001')
            ->setParliamentarySessionId(1)
            ->setAssemblyId(1)
            ->setIssueId(1)
            ->setKind(Model\KindEnum::A)
            ->setCongressmanId(1)
            ->setValidated(false);

        (new Speech())
            ->setDriver($this->getPDO())
            ->setEventDispatcher($eventDispatcher)
            ->save($speech)
        ;
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
                ['assembly_id' => 1, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value,],
                ['assembly_id' => 1, 'issue_id' => 2, 'kind' => Model\KindEnum::A->value,],
                ['assembly_id' => 1, 'issue_id' => 3, 'kind' => Model\KindEnum::A->value,],
                ['assembly_id' => 2, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value,],
                ['assembly_id' => 2, 'issue_id' => 2, 'kind' => Model\KindEnum::A->value,],
                ['assembly_id' => 2, 'issue_id' => 3, 'kind' => Model\KindEnum::A->value,],

                ['assembly_id' => 3, 'issue_id' => 1, 'kind' => Model\KindEnum::A->value,],
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'congressman 1', 'birth' => '2000-01-01'],
                ['congressman_id' => 2, 'name' => 'congressman 2', 'birth' => '2000-01-01'],
                ['congressman_id' => 3, 'name' => 'congressman 3', 'birth' => '2000-01-01'],
            ],
            'ParliamentarySession' => [
                ['parliamentary_session_id' => 1, 'assembly_id' => 1],
                ['parliamentary_session_id' => 2, 'assembly_id' => 1],
                ['parliamentary_session_id' => 3, 'assembly_id' => 1],
                ['parliamentary_session_id' => 1, 'assembly_id' => 3],
            ],
            'Speech' => [
                [
                    'speech_id' => 'id--00001',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                    'word_count' => 0,
                ],[
                    'speech_id' => 'id--00002',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                    'word_count' => 0,
                ],[
                    'speech_id' => 'id--00003',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                    'word_count' => 0,
                ],[
                    'speech_id' => 'id--00004',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 1,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                    'word_count' => 0,
                ],[
                    'speech_id' => 'id--10001',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => null,
                    'to' => null,
                    'validated' => true,
                    'word_count' => 0,
                ],[
                    'speech_id' => 'id--10002',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' => '2000-01-01 00:01:00',
                    'validated' => true,
                    'word_count' => 0,
                ],[
                    'speech_id' => 'id--10003',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 1,
                    'from' => '2000-02-01 00:00:00',
                    'to' => '2000-02-01 00:01:00',
                    'validated' => true,
                    'word_count' => 0,
                ],[
                    'speech_id' => 'id--10004',
                    'parliamentary_session_id' => 1,
                    'assembly_id' => 1,
                    'issue_id' => 2,
                    'kind' => Model\KindEnum::A->value,
                    'congressman_id' => 2,
                    'from' => '2000-03-01 00:00:00',
                    'to' => '2000-03-01 00:01:00',
                    'validated' => true,
                    'word_count' => 0,
                ]
            ],

        ]);
    }
}
