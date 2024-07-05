<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\MinistryController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(MinistryController::class)]
#[CoversMethod(MinistryController::class, 'setMinistryService')]
#[CoversMethod(MinistryController::class, 'get')]
#[CoversMethod(MinistryController::class, 'getList')]
#[CoversMethod(MinistryController::class, 'options')]
#[CoversMethod(MinistryController::class, 'optionsList')]
#[CoversMethod(MinistryController::class, 'patch')]
#[CoversMethod(MinistryController::class, 'put')]
class MinistryControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Ministry::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        $this->destroyServices();
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(new Model\Ministry())
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'GET');

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/radherraembaetti/144', 'GET');

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getListAll()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                new Model\Ministry(),
                new Model\Ministry(),
                new Model\Ministry(),
            ])
            ->getMock();

        $this->dispatch('/radherraembaetti', 'GET');

        $this->assertControllerName(MinistryController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putSuccessful()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
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

    #[Test]
    public function patchSuccessful()
    {
        $ministry = (new Model\Ministry())
            ->setMinistryId(144)
            ->setName('name');

        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn($ministry)
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

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
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

    #[Test]
    public function patchResourceNotFound()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
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

    #[Test]
    public function optionsSuccessful()
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

    #[Test]
    public function optionsList()
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
