<?php

namespace AlthingiTest\Service;

use Althingi\Service\Constituency;
use AlthingiTest\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Constituency as ConstituencyModel;

class ConstituencyTest extends TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $service = new Constituency();
        $service->setDriver($this->pdo);

        $expectedData = (new ConstituencyModel)
            ->setConstituencyId(1)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none');
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $constituency = (new ConstituencyModel())
            ->setName('name');

        $expectedTable = $this->createArrayDataSet([
            'Constituency' => [
                ['constituency_id' => 1, 'name' => 'some-place', 'abbr_short' => 's-p', 'abbr_long' => 'so-pl', 'description' => 'none'],
                ['constituency_id' => 2, 'name' => 'name', 'abbr_short' => null, 'abbr_long' => null, 'description' => null],
            ],
        ])->getTable('Constituency');
        $actualTable = $this->getConnection()->createQueryTable('Constituency', 'SELECT * FROM Constituency');

        $constituencyService = new Constituency();
        $constituencyService->setDriver($this->pdo);
        $constituencyService->create($constituency);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $constituency = (new ConstituencyModel())
            ->setName('name');

        $expectedTable = $this->createArrayDataSet([
            'Constituency' => [
                ['constituency_id' => 1, 'name' => 'some-place', 'abbr_short' => 's-p', 'abbr_long' => 'so-pl', 'description' => 'none'],
                ['constituency_id' => 2, 'name' => 'name', 'abbr_short' => null, 'abbr_long' => null, 'description' => null],
            ],
        ])->getTable('Constituency');
        $actualTable = $this->getConnection()->createQueryTable('Constituency', 'SELECT * FROM Constituency');

        $constituencyService = new Constituency();
        $constituencyService->setDriver($this->pdo);
        $constituencyService->save($constituency);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $constituency = (new ConstituencyModel())
            ->setConstituencyId(1)
            ->setName('another-place');

        $expectedTable = $this->createArrayDataSet([
            'Constituency' => [
                ['constituency_id' => 1, 'name' => 'another-place', 'abbr_short' => null, 'abbr_long' => null, 'description' => null],
            ],
        ])->getTable('Constituency');
        $actualTable = $this->getConnection()->createQueryTable('Constituency', 'SELECT * FROM Constituency');

        $constituencyService = new Constituency();
        $constituencyService->setDriver($this->pdo);
        $constituencyService->update($constituency);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Constituency' => [
                ['constituency_id' => 1, 'name' => 'some-place', 'abbr_short' => 's-p', 'abbr_long' => 'so-pl', 'description' => 'none']
            ],
        ]);
    }
}
