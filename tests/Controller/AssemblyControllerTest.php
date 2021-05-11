<?php

namespace AlthingiTest\Controller;

use Althingi\Service;
use Althingi\Model;
use Althingi\Controller\AssemblyController;
use Althingi\Model\Assembly;
use AlthingiTest\ServiceHelper;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use DateTime;

/**
 * Class AssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\AssemblyController
 *
 * @covers \Althingi\Controller\AssemblyController::setAssemblyService
 */
class AssemblyControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );

        $this->buildServices([
            Service\Assembly::class,
        ]);
    }

    public function tearDown(): void
    {
        $this->destroyServices();
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->andReturn((new Model\Assembly())
                ->setAssemblyId(144)
                ->setFrom(new DateTime())
            )
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetListAll()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new Assembly())->setAssemblyId(1)->setFrom(new DateTime()),
                (new Assembly())->setAssemblyId(2)->setFrom(new DateTime()),
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing', 'GET');

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
        ]);
        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidParams()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'to' => '2001-01-01',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $assembly = (new Model\Assembly())
            ->setAssemblyId(144)
            ->setFrom(new DateTime());

        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->andReturn($assembly)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidParams()
    {
        $assembly = (new Model\Assembly())
            ->setAssemblyId(144)
            ->setFrom(new DateTime('2000-01-01'));

        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->andReturn($assembly)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => 'invalid date',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '20016-01-01',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/144', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH'];
        $allow = $this->getResponse()
            ->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
                return trim($item);
            }, explode(',', count($allow) ? $allow[0] : ''));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $allow = $this->getResponse()
            ->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', count($allow) ? $allow[0] : ''));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
