<?php

namespace AlthingiTest\Controller;

use Althingi\Service\Cabinet;
use AlthingiTest\ServiceHelper;
use Mockery;
use Althingi\Service\Inflation;
use Althingi\Model\Inflation as InflationModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class InflationControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\InflationController
 * @covers \Althingi\Controller\InflationController::setInflationService
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
}
