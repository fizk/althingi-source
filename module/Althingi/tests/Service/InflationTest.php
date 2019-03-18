<?php

namespace AlthingiTest\Service;

use Althingi\Service\Inflation;
use AlthingiTest\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Inflation as InflationModel;
use DateTime;

class InflationTest extends TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->pdo);

        $expectedData = (new InflationModel())
            ->setId(1)
            ->setDate(new DateTime('2000-01-01'))
            ->setValue(1);

        $actualData = $inflationService->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAll()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->pdo);

        $expectedData = [
            (new InflationModel())->setId(1)->setDate(new DateTime('2000-01-01'))->setValue(1),
            (new InflationModel())->setId(2)->setDate(new DateTime('2000-01-02'))->setValue(2),
            (new InflationModel())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
            (new InflationModel())->setId(4)->setDate(new DateTime('2000-01-04'))->setValue(4),
            (new InflationModel())->setId(5)->setDate(new DateTime('2000-01-05'))->setValue(5),
        ];

        $actualData = $inflationService->fetchAll();

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllFrom()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->pdo);

        $expectedData = [
            (new InflationModel())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
            (new InflationModel())->setId(4)->setDate(new DateTime('2000-01-04'))->setValue(4),
            (new InflationModel())->setId(5)->setDate(new DateTime('2000-01-05'))->setValue(5),
        ];

        $actualData = $inflationService->fetchAll(new DateTime('2000-01-03'));

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllTo()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->pdo);

        $expectedData = [
            (new InflationModel())->setId(1)->setDate(new DateTime('2000-01-01'))->setValue(1),
            (new InflationModel())->setId(2)->setDate(new DateTime('2000-01-02'))->setValue(2),
            (new InflationModel())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
        ];

        $actualData = $inflationService->fetchAll(null, new DateTime('2000-01-03'));

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchAllFromAndTo()
    {
        $inflationService = new Inflation();
        $inflationService->setDriver($this->pdo);

        $expectedData = [
            (new InflationModel())->setId(2)->setDate(new DateTime('2000-01-02'))->setValue(2),
            (new InflationModel())->setId(3)->setDate(new DateTime('2000-01-03'))->setValue(3),
            (new InflationModel())->setId(4)->setDate(new DateTime('2000-01-04'))->setValue(4),
        ];

        $actualData = $inflationService->fetchAll(new DateTime('2000-01-02'), new DateTime('2000-01-04'));

        $this->assertEquals($expectedData, $actualData);
    }

    public function testSaveUpdate()
    {
        $inflation = (new InflationModel())
            ->setId(2)
            ->setDate(new DateTime('2000-01-02'))
            ->setValue(20);

        $expectedTable = $this->createArrayDataSet([
            'Inflation' => [
                ['id' => 1, 'date' => '2000-01-01', 'value' => 1],
                ['id' => 2, 'date' => '2000-01-02', 'value' => 20],
                ['id' => 3, 'date' => '2000-01-03', 'value' => 3],
                ['id' => 4, 'date' => '2000-01-04', 'value' => 4],
                ['id' => 5, 'date' => '2000-01-05', 'value' => 5],
            ],
        ])->getTable('Inflation');
        $actualTable = $this->getConnection()->createQueryTable('Inflation', 'SELECT * FROM Inflation');

        $assemblyService = new Inflation();
        $assemblyService->setDriver($this->pdo);
        $affectedRows = $assemblyService->save($inflation);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(2, $affectedRows);
    }

    public function testSaveCreate()
    {
        $inflation = (new InflationModel())
            ->setId(6)
            ->setDate(new DateTime('2000-01-06'))
            ->setValue(6);

        $expectedTable = $this->createArrayDataSet([
            'Inflation' => [
                ['id' => 1, 'date' => '2000-01-01', 'value' => 1],
                ['id' => 2, 'date' => '2000-01-02', 'value' => 2],
                ['id' => 3, 'date' => '2000-01-03', 'value' => 3],
                ['id' => 4, 'date' => '2000-01-04', 'value' => 4],
                ['id' => 5, 'date' => '2000-01-05', 'value' => 5],
                ['id' => 6, 'date' => '2000-01-06', 'value' => 6],
            ],
        ])->getTable('Inflation');
        $actualTable = $this->getConnection()->createQueryTable('Inflation', 'SELECT * FROM Inflation');

        $assemblyService = new Inflation();
        $assemblyService->setDriver($this->pdo);
        $affectedRows = $assemblyService->save($inflation);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    public function testUpdate()
    {
        $inflation = (new InflationModel())
            ->setId(2)
            ->setDate(new DateTime('2000-01-02'))
            ->setValue(20);

        $expectedTable = $this->createArrayDataSet([
            'Inflation' => [
                ['id' => 1, 'date' => '2000-01-01', 'value' => 1],
                ['id' => 2, 'date' => '2000-01-02', 'value' => 20],
                ['id' => 3, 'date' => '2000-01-03', 'value' => 3],
                ['id' => 4, 'date' => '2000-01-04', 'value' => 4],
                ['id' => 5, 'date' => '2000-01-05', 'value' => 5],
            ],
        ])->getTable('Inflation');
        $actualTable = $this->getConnection()->createQueryTable('Inflation', 'SELECT * FROM Inflation');

        $assemblyService = new Inflation();
        $assemblyService->setDriver($this->pdo);
        $affectedRows = $assemblyService->update($inflation);

        $this->assertTablesEqual($expectedTable, $actualTable);
        $this->assertEquals(1, $affectedRows);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Inflation' => [
                ['id' => 1, 'date' => '2000-01-01', 'value' => 1],
                ['id' => 2, 'date' => '2000-01-02', 'value' => 2],
                ['id' => 3, 'date' => '2000-01-03', 'value' => 3],
                ['id' => 4, 'date' => '2000-01-04', 'value' => 4],
                ['id' => 5, 'date' => '2000-01-05', 'value' => 5],
            ],
        ]);
    }
}
