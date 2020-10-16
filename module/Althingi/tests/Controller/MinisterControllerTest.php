<?php

namespace AlthingiTest\Controller;

use Althingi\Service;
use Althingi\Model;
use Althingi\Controller;
use AlthingiTest\ServiceHelper;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class IssueControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\MinisterController
 *
 * @covers \Althingi\Controller\MinisterController::setMinistryService
 */
class MinisterControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Service\Ministry::class
        ]);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::get
     * @throws \Exception
     */
    public function testGet()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('getByCongressmanAssembly')
            ->with(149, 1335, 321)
            ->andReturn(2)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/149/thingmenn/1335/radherra/321', 'GET');

        $this->assertControllerName(Controller\MinisterController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     * @throws \Exception
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('getByCongressmanAssembly')
            ->with(149, 1335, 321)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/149/thingmenn/1335/radherra/321', 'GET');

        $this->assertControllerName(Controller\MinisterController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     * @throws \Exception
     */
    public function testGetList()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('fetchByCongressmanAssembly')
            ->with(149, 1335)
            ->andReturn([(new Model\Ministry())])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/149/thingmenn/1335/radherra', 'GET');

        $this->assertControllerName(Controller\MinisterController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
