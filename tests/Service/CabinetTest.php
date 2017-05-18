<?php

namespace Althingi\Service;

use Althingi\DatabaseConnection;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\Cabinet as CabinetModel;

class CabinetTest extends PHPUnit_Extensions_Database_TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testFetchByAssembly()
    {
        $assemblyService = new Cabinet();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [
            (new CabinetModel())->setCabinetId(1)->setName('Cabinet name1')->setTitle('Cabinet title1'),
        ];

        $actualData = $assemblyService->fetchByAssembly(3);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByAssemblyMultiple()
    {
        $assemblyService = new Cabinet();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [
            (new CabinetModel())->setCabinetId(1)->setName('Cabinet name1')->setTitle('Cabinet title1'),
            (new CabinetModel())->setCabinetId(2)->setName('Cabinet name2')->setTitle('Cabinet title2'),
        ];

        $actualData = $assemblyService->fetchByAssembly(4);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testFetchByAssemblyNoResult()
    {
        $assemblyService = new Cabinet();
        $assemblyService->setDriver($this->pdo);

        $expectedData = [];

        $actualData = $assemblyService->fetchByAssembly(40);

        $this->assertEquals($expectedData, $actualData);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01'],
                ['assembly_id' => 2, 'from' => '2000-01-01'],
                ['assembly_id' => 3, 'from' => '2000-01-01'],
                ['assembly_id' => 4, 'from' => '2000-01-01'],
                ['assembly_id' => 5, 'from' => '2000-01-01'],
                ['assembly_id' => 6, 'from' => '2000-01-01'],
                ['assembly_id' => 7, 'from' => '2000-01-01'],
                ['assembly_id' => 8, 'from' => '2000-01-01'],
            ],
            'Cabinet' => [
                ['cabinet_id' => 1, 'name' => 'Cabinet name1', 'title' => 'Cabinet title1'],
                ['cabinet_id' => 2, 'name' => 'Cabinet name2', 'title' => 'Cabinet title2']
            ],
            'Cabinet_has_Assembly' => [
                ['assembly_id' => 1, 'cabinet_id' => 1],
                ['assembly_id' => 2, 'cabinet_id' => 1],
                ['assembly_id' => 3, 'cabinet_id' => 1],
                ['assembly_id' => 4, 'cabinet_id' => 1],
                ['assembly_id' => 4, 'cabinet_id' => 2],
                ['assembly_id' => 5, 'cabinet_id' => 2],
                ['assembly_id' => 6, 'cabinet_id' => 2],
                ['assembly_id' => 7, 'cabinet_id' => 2],
                ['assembly_id' => 8, 'cabinet_id' => 2],
            ]
        ]);
    }
}
