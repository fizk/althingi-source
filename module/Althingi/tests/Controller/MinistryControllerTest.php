<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\MinistryController;
use Althingi\Service;
use Althingi\Model;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\MinistryController
 * @covers \Althingi\Controller\MinistryController::setMinistryService
 */
class MinistryControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Service\Ministry::class,
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        \Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->andReturn(new Model\Ministry())
            ->once()
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'GET');

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'GET');

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    /**
     * @covers ::getList
     */
    public function testGetListAll()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                new Model\Ministry(),
                new Model\Ministry(),
                new Model\Ministry(),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/radherraembaetti', 'GET');

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
        $this->assertResponseHeaderContains('Access-Control-Expose-Headers', 'Range-Unit, Content-Range');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-3/3');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'PUT', [
            'ministry_id' => 144,
            'name' => 'name 1',
            'abbr_short' => 'abbr_short1',
            'abbr_long' => 'abbr_long1',
            'first' => 1,
            'last' => 1,
        ]);
        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidParams()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'PUT', [
            'ministry_id' => 144,
            'name' => 'name 1',
            'abbr_short' => 'abbr_short1',
            'abbr_long' => 'abbr_long1',
            'first' => 'thisissomething',
            'last' => 1,
        ]);
        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $ministry = (new Model\Ministry())
            ->setMinistryId(144)
            ->setName('name');

        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->andReturn($ministry)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'PATCH', [
            'name' => 'some new name',
        ]);

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'PATCH', [
            'name' => 'some new name',
        ]);

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidParams()
    {
        $ministry = (new Model\Ministry())
            ->setMinistryId(144)
            ->setName('name');

        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->andReturn($ministry)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'PATCH', [
            'name' => 'some new name',
            'first' => 'some invalid data'
        ]);

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'PATCH', [
            'name' => 'some new name',
        ]);

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/144', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
