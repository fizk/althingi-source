<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Model;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;

class ElectionTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $service = new Election();
        $service->setDriver($this->getPDO());

        $expectedData = (new Model\Election())
            ->setElectionId(1)
            ->setDate(new \DateTime('2000-01-01'));
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    #[Test]
    public function getByAssembly()
    {
        $service = new Election();
        $service->setDriver($this->getPDO());

        $election = $service->getByAssembly(1);
        $this->assertInstanceOf(Model\Election::class, $election);
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
