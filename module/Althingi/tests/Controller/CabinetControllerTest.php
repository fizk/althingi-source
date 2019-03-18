<?php

namespace AlthingiTest\Controller;

use Althingi\Service\Assembly;
use Althingi\Service\Cabinet;
use Althingi\Service\Congressman;
use Althingi\Service\Party;
use AlthingiTest\ServiceHelper;
use DateTime;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CabinetControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CabinetController
 * @covers \Althingi\Controller\CabinetController::setCongressmanService
 * @covers \Althingi\Controller\CabinetController::setPartyService
 * @covers \Althingi\Controller\CabinetController::setCabinetService
 * @covers \Althingi\Controller\CabinetController::setAssemblyService
 */
class CabinetControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );
        parent::setUp();
        $this->buildServices([
            Congressman::class,
            Party::class,
            Cabinet::class,
            Assembly::class,
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        $this->destroyServices();
        return parent::tearDown();
    }

    /**
     * @covers ::assemblyAction
     */
    public function testAssemblyAction()
    {
        $this->getMockService(Cabinet::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new \Althingi\Model\Cabinet())->setCabinetId(1)
            ])
            ->getMock();

        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\Assembly())
                    ->setFrom(new DateTime('2001-01-01'))
                    ->setTo(new DateTime('2001-01-01'))
                    ->setAssemblyId(1)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/1/raduneyti', 'GET');

        $this->assertControllerClass('CabinetController');
        $this->assertActionName('assembly');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Cabinet::class)
            ->shouldReceive('get')
            ->andReturn((new \Althingi\Model\Cabinet())->setCabinetId(1))
            ->getMock();

        $this->getMockService(Assembly::class)
            ->shouldReceive('fetchByCabinet')
            ->andReturn([
                (new \Althingi\Model\Assembly())
                    ->setFrom(new DateTime('2001-01-01'))
                    ->setTo(new DateTime('2001-01-01'))
                    ->setAssemblyId(1)
            ])
            ->getMock();

        $this->dispatch('/raduneyti/1', 'GET');

        $this->assertControllerClass('CabinetController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Cabinet::class)
            ->shouldReceive('fetchAll')
            ->andReturn([(new \Althingi\Model\Cabinet())->setCabinetId(1)])
            ->getMock();

        $this->dispatch('/raduneyti', 'GET');

        $this->assertControllerClass('CabinetController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Cabinet::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/raduneyti/1', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
            'title' => 'title',
            'description' => 'description',
        ]);
        $this->assertControllerClass('CabinetController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(Cabinet::class)
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock()

            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\Cabinet())
                    ->setCabinetId(1)
                    ->setFrom(new DateTime())
                    ->setTo(new DateTime())
            )
            ->getMock();

        $this->dispatch('/raduneyti/1', 'PATCH', [
            'title' => 'new title',
        ]);
        $this->assertControllerClass('CabinetController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
