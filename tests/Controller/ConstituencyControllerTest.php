<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\ConstituencyController;
use Althingi\Service\Constituency;
use Althingi\Model;
use AlthingiTest\ServiceHelper;
use Althingi\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ConstituencyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\ConstituencyController
 *
 * @covers \Althingi\Controller\ConstituencyController::setConstituencyService
 */
class ConstituencyControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Constituency::class,

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
        $this->getMockService(Constituency::class)
            ->shouldReceive('get')
            ->with(1)
            ->andReturn(new Model\Constituency())
            ->once()
            ->getMock();

        $this->dispatch('/kjordaemi/1');
        $this->assertControllerName(ConstituencyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Constituency::class)
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

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $this->getMockService(Constituency::class)
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

    /**
     * @covers ::put
     */
    public function testPutNoData()
    {
        $this->getMockService(Constituency::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/kjordaemi/1', 'PUT');
        $this->assertResponseStatusCode(201);
        $this->assertControllerName(ConstituencyController::class);
        $this->assertActionName('put');
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new Model\Constituency())
            ->setConstituencyId(101)
            ->setName('name1');

        $this->getMockService(Constituency::class)
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

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Constituency::class)
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
