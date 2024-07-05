<?php

namespace Althingi\Controller;

use Althingi\Controller\AssemblyController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(AssemblyController::class)]
#[CoversMethod(AssemblyController::class, 'get')]
#[CoversMethod(AssemblyController::class, 'getList')]
#[CoversMethod(AssemblyController::class, 'put')]
#[CoversMethod(AssemblyController::class, 'patch')]
#[CoversMethod(AssemblyController::class, 'options')]
#[CoversMethod(AssemblyController::class, 'optionsList')]
#[CoversMethod(AssemblyController::class, 'setAssemblyService')]
class AssemblyControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );

        $this->buildServices([
            Service\Assembly::class,
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
    public function getOneAssemblySuccessfully(): void
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn((new Model\Assembly())
                ->setAssemblyId(144)
                ->setFrom(new DateTime()))
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getOneAssemblyThatDoesNotExist(): void
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getListOfAllAssemblies(): void
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new Model\Assembly())->setAssemblyId(1)->setFrom(new DateTime()),
                (new Model\Assembly())->setAssemblyId(2)->setFrom(new DateTime()),
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing', 'GET');

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function makeAPutRequestForAssemblyThatWillBeSavedSuccessfully(): void
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
        ]);
        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function makeAPutReuestForAssemblyButValuesAreMissing(): void
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('create')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'to' => '2001-01-01',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function updatesExistingAssemblyWithAPatchReuestSuccessfully(): void
    {
        $assembly = (new Model\Assembly())
            ->setAssemblyId(144)
            ->setFrom(new DateTime());

        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn($assembly)
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function updatesAssemblyWithAPatchBytTheAssemblyDoesNotExist(): void
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function updatesAssemblyButValuesAreInvalid(): void
    {
        $assembly = (new Model\Assembly())
            ->setAssemblyId(144)
            ->setFrom(new DateTime('2000-01-01'));

        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn($assembly)
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => 'invalid date',
        ]);

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function getOptionsdHeaderForASingleAssembly(): void
    {
        $this->dispatch('/loggjafarthing/144', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH'];
        $allow = $this->getResponse()
            ->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
                return trim($item);
        }, explode(',', count($allow) ? $allow[0] : ''));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    #[Test]
    public function getOptionsdHeaderForAssemblyList(): void
    {
        $this->dispatch('/loggjafarthing', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $allow = $this->getResponse()
            ->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', count($allow) ? $allow[0] : ''));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
