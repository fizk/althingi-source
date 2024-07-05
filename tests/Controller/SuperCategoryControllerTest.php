<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\SuperCategoryController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(SuperCategoryController::class)]
#[CoversMethod(SuperCategoryController::class, 'setSuperCategoryService')]
#[CoversMethod(SuperCategoryController::class, 'get')]
#[CoversMethod(SuperCategoryController::class, 'getList')]
#[CoversMethod(SuperCategoryController::class, 'patch')]
#[CoversMethod(SuperCategoryController::class, 'put')]
class SuperCategoryControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\SuperCategory::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\SuperCategory::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn((new Model\SuperCategory()))
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1');

        $this->assertControllerName(SuperCategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\SuperCategory::class)
            ->shouldReceive('fetch')
            ->with()
            ->andReturn([new Model\SuperCategory()])
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar');

        $this->assertControllerName(SuperCategoryController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\SuperCategory::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1');

        $this->assertControllerName(SuperCategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function putSuccess()
    {
        $expectedData = (new Model\SuperCategory())
            ->setSuperCategoryId(1)
            ->setTitle('n1');

        $this->getMockService(Service\SuperCategory::class)
            ->shouldReceive('save')
            ->with(\Mockery::on(function ($actualDate) use ($expectedData) {
                return $actualDate == $expectedData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1', 'PUT', [
            'title' => 'n1',
        ]);

        $this->assertControllerName(SuperCategoryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putSuccessStuff()
    {
        $this->getMockService(Service\SuperCategory::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/not-a-number', 'PUT', [
            'title' => 'n1',
        ]);

        $this->assertControllerName(SuperCategoryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchSuccess()
    {
        $expectedData = (new Model\SuperCategory())
            ->setSuperCategoryId(1)
            ->setTitle('n2');

        $serverReturnedData = (new Model\SuperCategory())
            ->setSuperCategoryId(1)
            ->setTitle('n1');

        $this->getMockService(Service\SuperCategory::class)
            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $actualData == $expectedData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock()

            ->shouldReceive('get')
            ->once()
            ->andReturn($serverReturnedData)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1', 'PATCH', [
            'title' => 'n2',
        ]);

        $this->assertControllerName(SuperCategoryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function patchInvalidArgs()
    {
        $this->getMockService(Service\SuperCategory::class)
            ->shouldReceive('update')
            ->never()
            ->getMock()

            ->shouldReceive('get')
            ->once()
            ->andReturn(new Model\SuperCategory())
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1', 'PATCH', [
            'title' => 'title1',
        ]);

        $this->assertControllerName(SuperCategoryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\SuperCategory::class)
            ->shouldReceive('update')
            ->never()
            ->getMock()

            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1', 'PATCH', [
            'title' => 'n2',
        ]);

        $this->assertControllerName(SuperCategoryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
