<?php

namespace Althingi\Service;

use Althingi\DatabaseConnection;
use DateTime;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\President as PresidentModel;
use Althingi\Model\PresidentCongressman as PresidentCongressmanModel;

class PresidentTest extends PHPUnit_Extensions_Database_TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $presidentService = new President();
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

    public function testGetNotFound()
    {
        $presidentService = new President();
        $presidentService->setDriver($this->pdo);

        $expectedData = null;
        $actualData = $presidentService->getWithCongressman(1000);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetByUnique()
    {
        $presidentService = new President();
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

    public function testCreate()
    {
        $president = (new PresidentModel())
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('t')
            ->setFrom(new \DateTime('2001-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'President' => [
                ['president_id' => 1, 'congressman_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01', 'to' => null, 'title' => 't', 'abbr'=> null],
                ['president_id' => 2, 'congressman_id' => 1, 'assembly_id' => 1, 'from' => '2001-01-01', 'to' => null, 'title' => 't', 'abbr'=> null],
            ],
        ])->getTable('President');
        $actualTable = $this->getConnection()->createQueryTable('President', 'SELECT * FROM President');

        $presidentService = new President();
        $presidentService->setDriver($this->pdo);
        $presidentService->create($president);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $president = (new PresidentModel())
            ->setPresidentId(1)
            ->setCongressmanId(1)
            ->setAssemblyId(1)
            ->setTitle('newTitle')
            ->setFrom(new \DateTime('2000-01-01'));

        $expectedTable = $this->createArrayDataSet([
            'President' => [
                ['president_id' => 1, 'congressman_id' => 1, 'assembly_id' => 1, 'from' => '2000-01-01', 'to' => null, 'title' => 'newTitle', 'abbr'=> null],
            ],
        ])->getTable('President');
        $actualTable = $this->getConnection()->createQueryTable('President', 'SELECT * FROM President');

        $presidentService = new President();
        $presidentService->setDriver($this->pdo);
        $presidentService->update($president);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
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
