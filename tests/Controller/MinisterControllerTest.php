<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\MinisterController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(MinisterController::class)]
#[CoversMethod(MinisterController::class, 'setMinistryService')]
#[CoversMethod(MinisterController::class, 'get')]
#[CoversMethod(MinisterController::class, 'getList')]
class MinisterControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Ministry::class
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
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('getByCongressmanAssembly')
            ->with(149, 1335, 321)
            ->andReturn(new Model\Ministry())
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/149/thingmenn/1335/radherra/321', 'GET');

        $this->assertControllerName(MinisterController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('getByCongressmanAssembly')
            ->with(149, 1335, 321)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/149/thingmenn/1335/radherra/321', 'GET');

        $this->assertControllerName(MinisterController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\Ministry::class)
            ->shouldReceive('fetchByCongressmanAssembly')
            ->with(149, 1335)
            ->andReturn([(new Model\Ministry())])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/149/thingmenn/1335/radherra', 'GET');

        $this->assertControllerName(MinisterController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
