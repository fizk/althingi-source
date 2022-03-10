<?php

namespace Althingi\Service;

use DateTime;
use Althingi\Model\President;
use Althingi\Model\PresidentCongressman as PresidentCongressmanModel;
use Althingi\Service\President as PresidentService;
use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use PDO;

class PresidentTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);
        $expectedData = (new President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setFrom(new DateTime('2000-01-01'))
            ->setTitle('t');
        $actualData = $presidentService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetNotFound()
    {
        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);
        $expectedData = null;
        $actualData = $presidentService->get(10987655);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetWithCongressman()
    {
        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);

        $expectedData = (new PresidentCongressmanModel())
            ->setPresidentId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setCongressmanId(1)
            ->setName('name1')
            ->setFrom(new DateTime('2000-01-01'))
            ->setBirth(new DateTime('2000-01-01'));
        $actualData = $presidentService->getWithCongressman(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetWithCongressmanNotFound()
    {
        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);

        $expectedData = null;
        $actualData = $presidentService->getWithCongressman(1000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetByUnique()
    {
        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);

        $expectedData = (new PresidentCongressmanModel())
            ->setPresidentId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setCongressmanId(1)
            ->setName('name1')
            ->setFrom(new DateTime('2000-01-01'))
            ->setBirth(new DateTime('2000-01-01'));
        $actualData = $presidentService->getByUnique(1, 1, new DateTime('2000-01-01'), 't');

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllGeneratorAll()
    {
        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);
        $expectedData = [(new President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setFrom(new DateTime('2000-01-01'))
            ->setTitle('t')];

        $actualData = [];
        foreach($presidentService->fetchAllGenerator() as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllGeneratorByAssembly()
    {
        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);
        $expectedData = [(new President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setFrom(new DateTime('2000-01-01'))
            ->setTitle('t')];

        $actualData = [];
        foreach($presidentService->fetchAllGenerator(1) as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllGeneratorNotFound()
    {
        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);
        $expectedData = [];

        $actualData = [];
        foreach($presidentService->fetchAllGenerator(2) as $item) {
            $actualData[] = $item;
        }

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $president = (new President())
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setFrom(new \DateTime('2001-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'President' => [
                [
                    'president_id' => 1,
                    'congressman_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01',
                    'to' => null,
                    'title' => 't',
                    'abbr' => null
                ], [
                    'president_id' => 2,
                    'congressman_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2001-01-01',
                    'to' => null,
                    'title' => 't',
                    'abbr' => null
                ],
            ],
        ])->getTable('President');
        $actualTable = $this->getConnection()->createQueryTable('President', 'SELECT * FROM President');

        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);
        $presidentService->create($president);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testCreateAlreadyExist()
    {
        $president = (new President())
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setFrom(new \DateTime('2000-01-01'));

        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);
        try {
            $presidentService->create($president);
        } catch (\PDOException $e) {
            $this->assertEquals(1062, $e->errorInfo[1]);
        }
    }

    public function testUpdate()
    {
        $president = (new President())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('newTitle')
            ->setFrom(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'President' => [
                [
                    'president_id' => 1,
                    'congressman_id' => 1,
                    'assembly_id' => 1,
                    'from' => '2000-01-01',
                    'to' => null,
                    'title' => 'newTitle',
                    'abbr' => null
                ],
            ],
        ])->getTable('President');
        $actualTable = $this->getConnection()->createQueryTable('President', 'SELECT * FROM President');

        $presidentService = new PresidentService();
        $presidentService->setDriver($this->pdo);
        $presidentService->update($president);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null]
            ],
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name1', 'birth' => '2000-01-01'],
                ['congressman_id' => 2, 'name' => 'name2', 'birth' => '2000-01-01'],
                ['congressman_id' => 3, 'name' => 'name3', 'birth' => '2000-01-01'],
                ['congressman_id' => 4, 'name' => 'name4', 'birth' => '2000-01-01'],
            ],
            'President' => [
                ['president_id' => 1, 'congressman_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01', 'title' => 't']
            ]
        ]);
    }
}
