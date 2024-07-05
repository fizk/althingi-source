<?php

namespace Althingi\Controller;

use Althingi\Controller\CongressmanController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CongressmanController::class)]
#[CoversMethod(CongressmanController::class, 'setCongressmanService')]
#[CoversMethod(CongressmanController::class, 'assemblyAction')]
#[CoversMethod(CongressmanController::class, 'assemblyCongressmanAction')]
#[CoversMethod(CongressmanController::class, 'delete')]
#[CoversMethod(CongressmanController::class, 'get')]
#[CoversMethod(CongressmanController::class, 'getList')]
#[CoversMethod(CongressmanController::class, 'options')]
#[CoversMethod(CongressmanController::class, 'optionsList')]
#[CoversMethod(CongressmanController::class, 'patch')]
#[CoversMethod(CongressmanController::class, 'put')]
class CongressmanControllerTest extends TestCase
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
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessfull()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Congressman())
                ->setCongressmanId(1)
            )
            ->getMock();

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getResourceNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([new Model\Congressman()])
            ->getMock();

        $this->dispatch('/thingmenn', 'GET');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putSuccess()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PUT', [
            'name' => 'some name',
            'birth' => '1978-04-11'
        ]);

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putInvalidData()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PUT', [
            'name' => 'some name',
            'birth' => 'not a date'
        ]);

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchSuccess()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Congressman())
                    ->setCongressmanId(1)
                    ->setBirth(new \DateTime('1978-04-11'))
            )
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();


        $this->dispatch('/thingmenn/1', 'PATCH', [
            'name' => 'some name',
            'birth' => '1978-04-11'
        ]);

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function patchInvalidData()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Congressman())
                    ->setCongressmanId(1)
                    ->setName('My Namesson')
                    ->setBirth(new \DateTime('1978-04-11'))
            )
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();


        $this->dispatch('/thingmenn/1', 'PATCH', [
            'birth' => 'invalid date',
        ]);

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchResourceNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'PATCH', [
            'birth' => '1978-04-11',
        ]);

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function deleteSuccess()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Congressman())
                    ->setCongressmanId(1)
                    ->setName('My Namesson')
                    ->setBirth(new \DateTime('1978-04-11'))
            )
            ->getMock()
            ->shouldReceive('delete')
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function deleteResourceNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock()

            ->shouldReceive('delete')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function options()
    {
        $this->dispatch('/thingmenn/1', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'];
        $allowed = $this->getResponse()->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allowed[0]));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    #[Test]
    public function optionsList()
    {
        $this->dispatch('/thingmenn', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $allowed = $this->getResponse()->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allowed[0]));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    #[Test]
    public function assemblyAction()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function assemblyCongressmanAction()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->with(1)
            ->once()
            ->andReturn(new Model\Congressman())
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/1');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-congressman');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function assemblyCongressmanActionNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->with(1)
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmenn/1');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('assembly-congressman');
        $this->assertResponseStatusCode(404);
    }
}
