<?php

namespace Althingi\Controller;

use Althingi\Controller\MinistryController;
use Althingi\Model;
use Althingi\Service;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class AssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\MinistryController
 *
 * @covers \Althingi\Controller\MinistryController::setMinistryService
 */
class MinistryControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Ministry::class,
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
     * @todo this shold work but currently: 'first' => 'thisissomething', is converted into NULL
     */
    // public function testPutInvalidParams()
    // {
    //     $this->getMockService(Service\Ministry::class)
    //         ->shouldReceive('save')
    //         ->andReturn(1)
    //         ->never()
    //         ->getMock();

    //     $this->dispatch('/radherraembaetti/144', 'PUT', [
    //         'ministry_id' => 144,
    //         'name' => 'name 1',
    //         'abbr_short' => 'abbr_short1',
    //         'abbr_long' => 'abbr_long1',
    //         'first' => 'thisissomething',
    //         'last' => 1,
    //     ]);
    //     $this->assertControllerName(MinistryController::class);
    //     $this->assertActionName('put');
    //     $this->assertResponseStatusCode(400);
    // }

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
     * @todo this shold work but currently: 'first' => 'thisissomething', is converted into NULL
     */
    // public function testPatchInvalidParams()
    // {
    //     $ministry = (new Model\Ministry())
    //         ->setMinistryId(144)
    //         ->setName('name');

    //     $this->getMockService(Service\Ministry::class)
    //         ->shouldReceive('get')
    //         ->andReturn($ministry)
    //         ->once()
    //         ->getMock()
    //         ->shouldReceive('update')
    //         ->andReturn(1)
    //         ->getMock();

    //     $this->dispatch('/radherraembaetti/144', 'PATCH', [
    //         'name' => 'some new name',
    //         'first' => 'some invalid data'
    //     ]);

    //     $this->assertControllerName(MinistryController::class);
    //     $this->assertActionName('patch');
    //     $this->assertResponseStatusCode(400);
    // }

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
        $this->dispatch('/radherraembaetti/144', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'];
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
        $this->dispatch('/radherraembaetti', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $allow = $this->getResponse()
            ->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', count($allow) ? $allow[0] : ''));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
