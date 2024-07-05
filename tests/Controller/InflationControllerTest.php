<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\InflationController;
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(InflationController::class)]
#[CoversMethod(InflationController::class, 'setInflationService')]
#[CoversMethod(InflationController::class, 'setCabinetService')]
#[CoversMethod(InflationController::class, 'setAssemblyService')]
#[CoversMethod(InflationController::class, 'fetchAssemblyAction')]
#[CoversMethod(InflationController::class, 'get')]
#[CoversMethod(InflationController::class, 'getList')]
#[CoversMethod(InflationController::class, 'patch')]
#[CoversMethod(InflationController::class, 'put')]
class InflationControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Inflation::class,
            Service\Cabinet::class,
            Service\Assembly::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        $this->destroyServices();
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\Inflation::class)
            ->shouldReceive('get')
            ->withArgs([14])
            ->andReturn(
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime())
            )
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga/14', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\Inflation::class)
            ->shouldReceive('get')
            ->withArgs([14])
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/verdbolga/14', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\Inflation::class)
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
            ])
            ->getMock();

        $this->dispatch('/verdbolga', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function getListWithAssembly()
    {
        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->once()
            ->andReturn([
                (new Model\Cabinet())
                    ->setFrom(new DateTime())
                    ->setTo(new DateTime()),
            ])
            ->getMock();

        $this->getMockService(Service\Inflation::class)
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
            ])
            ->getMock();

        $this->dispatch('/verdbolga?loggjafarthing=1', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function fetchAssembly()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Assembly())
                    ->setAssemblyId(1)
                    ->setFrom(new DateTime())
                    ->setTo(new DateTime())
            )
            ->getMock();

        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->once()
            ->andReturn([
                (new Model\Cabinet())
            ])
            ->getMock();

        $this->getMockService(Service\Inflation::class)
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
                (new Model\Inflation())
                    ->setId(1)
                    ->setValue(1)
                    ->setDate(new \DateTime()),
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/verdbolga', 'GET');

        $this->assertControllerName(InflationController::class);
        $this->assertActionName('fetch-assembly');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putSuccessful()
    {
        $this->getMockService(Service\Inflation::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/verdbolga/1', 'PUT', [
            'value' => 1,
            'date' => '2001-01-01'
        ]);
        echo $this->getResponse()->getBody()->__toString();
        $this->assertControllerName(InflationController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function patchSuccessful()
    {
        $this->getMockService(Service\Inflation::class)
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock()

            ->shouldReceive('get')
            ->andReturn(
                (new Model\Inflation())
                ->setId(1)
                ->setValue(0)
                ->setDate(new DateTime())
            )
            ->getMock();

        $this->dispatch('/verdbolga/1', 'PATCH', [
            'value' => 1,
            'date' => '2001-01-01'
        ]);
        $this->assertControllerName(InflationController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
