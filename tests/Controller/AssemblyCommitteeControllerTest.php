<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\AssemblyCommitteeController;
use AlthingiTest\ServiceHelper;
use Althingi\Service\Committee;
use Althingi\Model\Committee as CommitteeModel;
use Laminas\ServiceManager\ServiceManager;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class AssemblyCommitteeControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\AssemblyCommitteeController
 *
 * @covers \Althingi\Controller\AssemblyCommitteeController::setCommitteeService
 */
class AssemblyCommitteeControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );

        $this->buildServices([
            Committee::class,
        ]);
    }

    public function tearDown(): void
    {
        $this->destroyServices();
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('get')
            ->withArgs([1])
            ->andReturn((new CommitteeModel())->setCommitteeId(1)->setFirstAssemblyId(1))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir/1', 'GET');

        $this->assertControllerName(AssemblyCommitteeController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('get')
            ->withArgs([1])
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir/1', 'GET');

        $this->assertControllerName(AssemblyCommitteeController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('fetchByAssembly')
            ->withArgs([144])
            ->andReturn([
                (new CommitteeModel())->setCommitteeId(1)->setFirstAssemblyId(100),
                (new CommitteeModel())->setCommitteeId(2)->setFirstAssemblyId(100),
                (new CommitteeModel())->setCommitteeId(3)->setFirstAssemblyId(100),
                (new CommitteeModel())->setCommitteeId(4)->setFirstAssemblyId(100),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir', 'GET');

        $this->assertControllerName(AssemblyCommitteeController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
