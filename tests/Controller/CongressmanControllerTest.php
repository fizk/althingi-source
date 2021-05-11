<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\CongressmanController;
use AlthingiTest\ServiceHelper;
use Althingi\Service;
use Althingi\Model;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CongressmanControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanController
 *
 * @covers \Althingi\Controller\CongressmanController::setCongressmanService
 */
class CongressmanControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Congressman::class,
        ]);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
                ->setCongressmanId(1)
            )->once()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'GET');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetResourceNotFound()
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

    /**
     * @covers ::getList
     */
    public function testGetList()
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

    /**
     * @covers ::put
     */
    public function testPutSuccess()
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

    /**
     * @covers ::put
     */
    public function testPutInvalidData()
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

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
                    ->setCongressmanId(1)
                    ->setBirth(new \DateTime('1978-04-11'))
            )->once()
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

    /**
     * @covers ::patch
     */
    public function testPatchInvalidData()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
                    ->setCongressmanId(1)
                    ->setName('My Namesson')
                    ->setBirth(new \DateTime('1978-04-11'))
            )->once()
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

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(null)->once()
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

    /**
     * @covers ::delete
     */
    public function testDeleteSuccess()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Congressman())
                    ->setCongressmanId(1)
                    ->setName('My Namesson')
                    ->setBirth(new \DateTime('1978-04-11'))
            )->once()
            ->getMock()

            ->shouldReceive('delete')
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::delete
     */
    public function testDeleteResourceNotFound()
    {
        $this->getMockService(Service\Congressman::class)
            ->shouldReceive('get')
            ->andReturn(null)->once()
            ->getMock()

            ->shouldReceive('delete')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/1', 'DELETE');

        $this->assertControllerName(CongressmanController::class);
        $this->assertActionName('delete');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/thingmenn/1', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'];
        $allowed = $this->getResponse()->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allowed[0]));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/thingmenn', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $allowed = $this->getResponse()->getHeader('Allow');
        $actualMethods = array_map(function ($item) {
            return trim($item);
        }, explode(',', $allowed[0]));

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::assemblyAction
     */
    public function testAssemblyAction()
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

    /**
     * @covers ::assemblyCongressmanAction
     */
    public function testAssemblyCongressmanAction()
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

    /**
     * @covers ::assemblyCongressmanAction
     */
    public function testAssemblyCongressmanActionNotFound()
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
