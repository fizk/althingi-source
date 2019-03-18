<?php

namespace AlthingiTest\Controller;

use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use AlthingiTest\ServiceHelper;
use Mockery;
use Althingi\Service\Inflation;
use Althingi\Model\Inflation as InflationModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use DateTime;

/**
 * Class InflationControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\InflationController
 * @covers \Althingi\Controller\InflationController::setInflationService
 * @covers \Althingi\Controller\InflationController::setCabinetService
 * @covers \Althingi\Controller\InflationController::setAssemblyService
 */
class InflationControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Inflation::class,
            Cabinet::class,
            Assembly::class,
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Inflation::class)
            ->shouldReceive('get')
            ->withArgs([14])
            ->andReturn((new InflationModel())->setId(1)->setValue(1)->setDate(new \DateTime()))
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga/14', 'GET');

        $this->assertControllerClass('InflationController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
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

        $this->assertControllerClass('InflationController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Inflation::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new InflationModel()),
                (new InflationModel()),
                (new InflationModel()),
                (new InflationModel()),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga', 'GET');

        $this->assertControllerClass('InflationController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-4/4');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
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
                (new InflationModel()),
                (new InflationModel()),
                (new InflationModel()),
                (new InflationModel()),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga?loggjafarthing=1', 'GET');

        $this->assertControllerClass('InflationController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-4/4');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
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
                (new InflationModel()),
                (new InflationModel()),
                (new InflationModel()),
                (new InflationModel()),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/verdbolga', 'GET');

        $this->assertControllerClass('InflationController');
        $this->assertActionName('fetch-assembly');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-4/4');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
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
        $this->assertControllerClass('InflationController');
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
        $this->assertControllerClass('InflationController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
