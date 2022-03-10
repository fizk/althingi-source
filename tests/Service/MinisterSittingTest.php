<?php

namespace Althingi\Service;

use Althingi\Service\MinisterSitting;
use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model;
use PDO;

class MinisterSittingTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $ministerSittingService = new MinisterSitting();
        $ministerSittingService->setDriver($this->pdo);

        $expectedData = (new Model\MinisterSitting())
            ->setAssemblyId(1)
            ->setMinisterSittingId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        $actualData = $ministerSittingService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllGeneratorAll()
    {
        $ministerSittingService = new MinisterSitting();
        $ministerSittingService->setDriver($this->pdo);

        $expectedData = [(new Model\MinisterSitting())
            ->setAssemblyId(1)
            ->setMinisterSittingId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))]
        ;

        $actualData = [];
        foreach ($ministerSittingService->fetchAllGenerator() as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllGeneratorByAssemblyFound()
    {
        $ministerSittingService = new MinisterSitting();
        $ministerSittingService->setDriver($this->pdo);

        $expectedData = [(new Model\MinisterSitting())
            ->setAssemblyId(1)
            ->setMinisterSittingId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))]
        ;

        $actualData = [];
        foreach ($ministerSittingService->fetchAllGenerator(1) as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllGeneratorByAssemblyNotFound()
    {
        $ministerSittingService = new MinisterSitting();
        $ministerSittingService->setDriver($this->pdo);

        $expectedData = []
        ;

        $actualData = [];
        foreach ($ministerSittingService->fetchAllGenerator(2) as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $ministrySitting = (new Model\MinisterSitting())
            ->setAssemblyId(1)
            ->setMinisterSittingId(2)
            ->setMinistryId(2)
            ->setCongressmanId(2)
            ->setPartyId(2)
            ->setFrom(new \DateTime('2001-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'MinisterSitting' => [
                [
                    'minister_sitting_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                [
                    'minister_sitting_id' => 2,
                    'assembly_id' => 1,
                    'ministry_id' => 2,
                    'congressman_id' => 2,
                    'party_id' => 2,
                    'from' => '2001-01-01',
                    'to' => null,
                ]
            ],
        ])->getTable('MinisterSitting');
        $actualTable = $this->getConnection()->createQueryTable('MinisterSitting', 'SELECT * FROM MinisterSitting');

        $ministrySittingService = new MinisterSitting();
        $ministrySittingService->setDriver($this->pdo);
        $ministrySittingService->create($ministrySitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testCreateAlreadyExist()
    {
        $ministrySitting = (new Model\MinisterSitting())
            ->setAssemblyId(1)
            ->setMinisterSittingId(2)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(2)
            ->setFrom(new \DateTime('2001-01-01'));

        $ministrySittingService = new MinisterSitting();
        $ministrySittingService->setDriver($this->pdo);
        try {
            $ministrySittingService->create($ministrySitting);
        } catch (\PDOException $e) {
            $this->assertEquals(1062, $e->errorInfo[1]);
        }
    }

    public function testSaveUpdate()
    {
        $ministrySitting = (new Model\MinisterSitting())
            ->setAssemblyId(1)
            ->setMinisterSittingId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        $expectedTable = $this->createArrayDataSet([
            'MinisterSitting' => [
                [
                    'minister_sitting_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ]
            ],
        ])->getTable('MinisterSitting');
        $actualTable = $this->getConnection()->createQueryTable('MinisterSitting', 'SELECT * FROM MinisterSitting');

        $ministrySittingService = new MinisterSitting();
        $ministrySittingService->setDriver($this->pdo);
        $affectedRows = $ministrySittingService->save($ministrySitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(2, $affectedRows);
    }

    public function testSaveCreate()
    {
        $ministrySitting = (new Model\MinisterSitting())
            ->setAssemblyId(2)
            ->setMinisterSittingId(2)
            ->setMinistryId(2)
            ->setCongressmanId(2)
            ->setPartyId(2)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        $expectedTable = $this->createArrayDataSet([
            'MinisterSitting' => [
                [
                    'minister_sitting_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ],
                [
                    'minister_sitting_id' => 2,
                    'assembly_id' => 2,
                    'ministry_id' => 2,
                    'congressman_id' => 2,
                    'party_id' => 2,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ]
            ],
        ])->getTable('MinisterSitting');
        $actualTable = $this->getConnection()->createQueryTable('MinisterSitting', 'SELECT * FROM MinisterSitting');

        $ministrySittingService = new MinisterSitting();
        $ministrySittingService->setDriver($this->pdo);
        $affectedRows = $ministrySittingService->save($ministrySitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    public function testUpdate()
    {
        $ministrySitting = (new Model\MinisterSitting())
            ->setAssemblyId(1)
            ->setMinisterSittingId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;

        $expectedTable = $this->createArrayDataSet([
            'MinisterSitting' => [
                [
                    'minister_sitting_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => '2001-01-01',
                ]
            ],
        ])->getTable('MinisterSitting');
        $actualTable = $this->getConnection()->createQueryTable('MinisterSitting', 'SELECT * FROM MinisterSitting');

        $ministrySittingService = new MinisterSitting();
        $ministrySittingService->setDriver($this->pdo);
        $ministrySittingService->update($ministrySitting);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testGetIdentifier()
    {
        $ministerSittingService = new MinisterSitting();
        $ministerSittingService->setDriver($this->pdo);

        $expectedData = (new Model\MinisterSitting())
            ->setAssemblyId(1)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        $actualData = $ministerSittingService->getIdentifier(
            $expectedData->getAssemblyId(),
            $expectedData->getMinistryId(),
            $expectedData->getCongressmanId(),
            $expectedData->getFrom()
        );

        $this->assertEquals(1, $actualData);
    }

    public function testGetIdentifierNotFound()
    {
        $ministerSittingService = new MinisterSitting();
        $ministerSittingService->setDriver($this->pdo);

        $expectedData = (new Model\MinisterSitting())
            ->setAssemblyId(100)
            ->setMinistryId(1)
            ->setCongressmanId(1)
            ->setPartyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        $actualData = $ministerSittingService->getIdentifier(
            $expectedData->getAssemblyId(),
            $expectedData->getMinistryId(),
            $expectedData->getCongressmanId(),
            $expectedData->getFrom()
        );

        $this->assertEquals(false, $actualData);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 3, 'from' => '2000-01-01', 'to' => null],
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 2, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
                ['congressman_id' => 3, 'name' => 'name1', 'birth' => '2000-01-01', 'death' => null],
            ],
            'Party' => [
                ['party_id' => 1, 'name' => 'p1', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 2, 'name' => 'p2', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
                ['party_id' => 3, 'name' => 'p3', 'abbr_short' => null, 'abbr_long' => null, 'color' => 'ffffff'],
            ],
            'Ministry' => [
                [
                    'ministry_id' => 1,
                    'name' => 'name 1',
                    'abbr_short' => 'abbr_short1',
                    'abbr_long' => 'abbr_long1',
                    'first' => 1,
                    'last' => 3,
                ],
                [
                    'ministry_id' => 2,
                    'name' => 'name 2',
                    'abbr_short' => 'abbr_short2',
                    'abbr_long' => 'abbr_long2',
                    'first' => 1,
                    'last' => null,
                ],
            ],
            'MinisterSitting' => [
                [
                    'minister_sitting_id' => 1,
                    'assembly_id' => 1,
                    'ministry_id' => 1,
                    'congressman_id' => 1,
                    'party_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                ]
            ],
        ]);
    }
}
