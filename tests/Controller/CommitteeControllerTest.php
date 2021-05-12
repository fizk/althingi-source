<?php

namespace Althingi\Controller;

use Althingi\Controller\CommitteeController;
use Althingi\Service;
use Althingi\Model;
use Althingi\ServiceHelper;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Mockery;

/**
 * Class CommitteeControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CommitteeController
 *
 * @covers \Althingi\Controller\CommitteeController::setCommitteeService
 */
class CommitteeControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Committee::class,
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
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn((new Model\Committee())->setCommitteeId(1)->setFirstAssemblyId(1))
            ->getMock();

        $this->dispatch('/nefndir/1', 'GET');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/nefndir/1', 'GET');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (new Model\Committee())->setCommitteeId(1)->setFirstAssemblyId(1),
                (new Model\Committee())->setCommitteeId(2)->setFirstAssemblyId(1),
                (new Model\Committee())->setCommitteeId(3)->setFirstAssemblyId(1),
            ])
            ->getMock();

        $this->dispatch('/nefndir', 'GET');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PUT', [
            'first_assembly_id' => 1,
            'last_assembly_id' => 1,
            'name' => 'name',
            'abbr_short' => 'n',
            'abbr_long' => 'na',

        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidParameters()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('create')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PUT', []);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Committee())
                ->setCommitteeId(1)
                ->setFirstAssemblyId(1)
                ->setLastAssemblyId(1)
                ->setName('name')
                ->setAbbrShort('n')
                ->setAbbrLong('na')
            )
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidForm()
    {
        $this->getMockService(Service\Committee::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Committee())
                ->setCommitteeId(1)
                ->setFirstAssemblyId(1)
                ->setLastAssemblyId(1)
                ->setAbbrShort('n')
                ->setAbbrLong('na')
            )
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/nefndir/1', 'OPTIONS');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);
        $this->assertHasResponseHeader('Allow');
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/nefndir', 'OPTIONS');

        $this->assertControllerName(CommitteeController::class);
        $this->assertActionName('optionsList');
        $this->assertResponseStatusCode(200);
        $this->assertHasResponseHeader('Allow');
    }
}
