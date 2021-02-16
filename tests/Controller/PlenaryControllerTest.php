<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\PlenaryController;
use Althingi\Service\Plenary;
use AlthingiTest\ServiceHelper;
use Althingi\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class PlenaryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\PlenaryController
 *
 * @covers \Althingi\Controller\PlenaryController::setPlenaryService
 */
class PlenaryControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );

        $this->buildServices([
            Plenary::class,
        ]);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $expectedData = (new \Althingi\Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(2)
            ->setName('n1')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;
        $this->getMockService(Plenary::class)
            ->shouldReceive('save')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'PUT', [
            'from' => '2001-01-01 00:00',
            'to' => '2001-01-01 00:00',
            'name' => 'n1'
        ]);

        $this->assertControllerName(PlenaryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new \Althingi\Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(2)
            ->setName('newName')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;
        $this->getMockService(Plenary::class)
            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\Plenary())
                    ->setAssemblyId(1)
                    ->setPlenaryId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'))
            )
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'PATCH', [
            'name' => 'newName'
        ]);

        $this->assertControllerName(PlenaryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Plenary::class)
            ->shouldReceive('get')
            ->with(1, 2)
            ->andReturn(
                (new \Althingi\Model\Plenary())
                    ->setAssemblyId(1)
                    ->setPlenaryId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'))
            )
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'GET');
        $this->assertControllerName(PlenaryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Plenary::class)
            ->shouldReceive('get')
            ->with(1, 2)
            ->andReturn(null)
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'GET');
        $this->assertControllerName(PlenaryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Plenary::class)
            ->shouldReceive('countByAssembly')
            ->andReturn(123)
            ->once()
            ->getMock()

            ->shouldReceive('fetchByAssembly')
            ->andReturn(array_map(function () {
                return (new \Althingi\Model\Plenary())
                    ->setAssemblyId(1)
                    ->setPlenaryId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'));
            }, range(0, 24)))
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir', 'GET');
        $this->assertControllerName(PlenaryController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
