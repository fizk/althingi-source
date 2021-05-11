<?php

namespace Althingi\Service;

use Althingi\Service\Election;
use Althingi\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Election as ElectionModel;
use PDO;

class ElectionTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $service = new Election();
        $service->setDriver($this->pdo);

        $expectedData = (new ElectionModel())
            ->setElectionId(1)
            ->setDate(new \DateTime('2000-01-01'));
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testGetByAssembly()
    {
        $service = new Election();
        $service->setDriver($this->pdo);

        $election = $service->getByAssembly(1);
        $this->assertInstanceOf(ElectionModel::class, $election);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01'],
                ['assembly_id' => 2, 'from' => '2000-01-01']
            ],
            'Election' => [
                ['election_id' => 1, 'date' => '2000-01-01', 'title' => null, 'description' => null],
                ['election_id' => 2, 'date' => '2000-01-01', 'title' => null, 'description' => null],
                ['election_id' => 3, 'date' => '2000-01-01', 'title' => null, 'description' => null],
                ['election_id' => 4, 'date' => '2000-01-01', 'title' => null, 'description' => null],
            ],
            'Election_has_Assembly' => [
                ['election_id' => 1, 'assembly_id' => 1],
                ['election_id' => 2, 'assembly_id' => 2],
            ]
        ]);
    }
}
