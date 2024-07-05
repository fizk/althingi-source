<?php

namespace Althingi\Controller;

use Althingi\Controller\CabinetController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CabinetController::class)]
#[CoversMethod(CabinetController::class, 'setCabinetService')]
#[CoversMethod(CabinetController::class, 'setAssemblyService')]
#[CoversMethod(CabinetController::class, 'assemblyAction')]
#[CoversMethod(CabinetController::class, 'get')]
#[CoversMethod(CabinetController::class, 'getList')]
#[CoversMethod(CabinetController::class, 'put')]
#[CoversMethod(CabinetController::class, 'patch')]
class CabinetControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Congressman::class,
            Service\Party::class,
            Service\Cabinet::class,
            Service\Assembly::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        $this->destroyServices();
        parent::tearDown();
    }

    #[Test]
    public function getAllCabinetsForAssemblySuccessfully()
    {
        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new Model\Cabinet())->setCabinetId(1)
            ])
            ->getMock();

        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Assembly())
                    ->setFrom(new DateTime('2001-01-01'))
                    ->setTo(new DateTime('2001-01-01'))
                    ->setAssemblyId(1)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/1/raduneyti', 'GET');

        $this->assertControllerName(CabinetController::class);
        $this->assertActionName('assembly');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function getCabinetSuccessfully()
    {
        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('get')
            ->andReturn((new Model\Cabinet())->setCabinetId(1))
            ->getMock();

        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('fetchByCabinet')
            ->andReturn([
                (new Model\Assembly())
                    ->setFrom(new DateTime('2001-01-01'))
                    ->setTo(new DateTime('2001-01-01'))
                    ->setAssemblyId(1)
            ])
            ->getMock();

        $this->dispatch('/raduneyti/1', 'GET');

        $this->assertControllerName(CabinetController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getListOfAllCabinets()
    {
        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('fetchAll')
            ->andReturn([(new Model\Cabinet())->setCabinetId(1)])
            ->getMock();

        $this->dispatch('/raduneyti', 'GET');

        $this->assertControllerName(CabinetController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putCabinetSavingItSuccessfully()
    {
        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/raduneyti/1', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
            'title' => 'title',
            'description' => 'description',
        ]);
        $this->assertControllerName(CabinetController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function patchCabinetSuccessfully()
    {
        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock()

            ->shouldReceive('get')
            ->andReturn(
                (new Model\Cabinet())
                    ->setCabinetId(1)
                    ->setFrom(new DateTime())
                    ->setTo(new DateTime())
            )
            ->getMock();

        $this->dispatch('/raduneyti/1', 'PATCH', [
            'title' => 'new title',
        ]);
        $this->assertControllerName(CabinetController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
