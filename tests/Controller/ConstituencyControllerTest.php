<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\ConstituencyController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(ConstituencyController::class)]
#[CoversMethod(ConstituencyController::class, 'setConstituencyService')]
#[CoversMethod(ConstituencyController::class, 'get')]
#[CoversMethod(ConstituencyController::class, 'put')]
#[CoversMethod(ConstituencyController::class, 'patch')]
class ConstituencyControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Constituency::class,

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
        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn((new Model\Constituency())->setConstituencyId(1))
            ->once()
            ->getMock();

        $this->dispatch('/kjordaemi/1');
        $this->assertControllerName(ConstituencyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/kjordaemi/1');
        $this->assertControllerName(ConstituencyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function putSuccess()
    {
        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/kjordaemi/1', 'PUT', [
            'name' => 'name1'
        ]);
        $this->assertResponseStatusCode(201);
        $this->assertControllerName(ConstituencyController::class);
        $this->assertActionName('put');
    }

    #[Test]
    public function putNoData()
    {
        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/kjordaemi/1', 'PUT');
        $this->assertResponseStatusCode(201);
        $this->assertControllerName(ConstituencyController::class);
        $this->assertActionName('put');
    }

    #[Test]
    public function patchSuccess()
    {
        $expectedData = (new Model\Constituency())
            ->setConstituencyId(101)
            ->setName('name1');

        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('get')
            ->with(101)
            ->andReturn(
                (new Model\Constituency())
                    ->setConstituencyId(101)
                    ->setName('some name')
            )
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualDate) use ($expectedData) {
                return $actualDate == $expectedData;
            }))
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/kjordaemi/101', 'PATCH', [
            'name' => 'name1'
        ]);
        $this->assertResponseStatusCode(205);
        $this->assertControllerName(ConstituencyController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\Constituency::class)
            ->shouldReceive('get')
            ->with(101)
            ->andReturn(null)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/kjordaemi/101', 'PATCH', [
            'name' => 'name1'
        ]);
        $this->assertResponseStatusCode(404);
        $this->assertControllerName(ConstituencyController::class);
        $this->assertActionName('patch');
    }
}
