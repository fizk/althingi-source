<?php

namespace Althingi\Controller;

use Althingi\Controller\InflationController;
use Althingi\Model\Inflation as InflationModel;
use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Inflation;
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Class InflationControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\InflationController
 *
 * @covers \Althingi\Controller\InflationController::setInflationService
 * @covers \Althingi\Controller\InflationController::setCabinetService
 * @covers \Althingi\Controller\InflationController::setAssemblyService
 */
class InflationControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Inflation::class,
            Cabinet::class,
            Assembly::class,
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
        $this->getMockService(Inflation::class)
            ->shouldReceive('get')
            ->withArgs([14])
            ->andReturn(
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime())
            )
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga/14', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Inflation::class)
            ->shouldReceive('get')
            ->withArgs([14])
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga/14', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Inflation::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::getList
     */
    public function testGetListWithAssembly()
    {
        $this->getMockService(Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([
                (new \Althingi\Model\Cabinet())
                    ->setFrom(new DateTime())
                    ->setTo(new DateTime()),
            ])
            ->once()
            ->getMock();

        $this->getMockService(Inflation::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga?loggjafarthing=1', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::fetchAssemblyAction
     */
    public function testFetchAssembly()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\Assembly())
                    ->setAssemblyId(1)
                    ->setFrom(new DateTime())
                    ->setTo(new DateTime())
            )
            ->once()
            ->getMock();

        $this->getMockService(Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([
                (new \Althingi\Model\Cabinet())
            ])
            ->once()
            ->getMock();

        $this->getMockService(Inflation::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new InflationModel())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/verdbolga', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('fetch-assembly');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Inflation::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/verdbolga/1', 'PUT', [
            'value' => 1,
            'date' => '2001-01-01'
        ]);
        echo $this->getResponse()->getBody()->__toString();
        $this->assertControllerName(InflationController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(Inflation::class)
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock()

            ->shouldReceive('get')
            ->andReturn(
                (new InflationModel())
                ->setId(1)
                ->setValue(0)
                ->setDate(new DateTime())
            )
            ->getMock();

        $this->dispatch('/verdbolga/1', 'PATCH', [
            'value' => 1,
            'date' => '2001-01-01'
        ]);
        $this->assertControllerName(InflationController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
