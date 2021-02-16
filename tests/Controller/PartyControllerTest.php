<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\PartyController;
use Althingi\Service;
use Althingi\Model;
use AlthingiTest\ServiceHelper;
use Althingi\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class PartyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\PartyController
 *
 * @covers \Althingi\Controller\PartyController::setPartyService
 */
class PartyControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Party::class,
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
        $this->getMockService(Service\Party::class)
            ->shouldReceive('get')
            ->with(100)
            ->andReturn(new Model\Party())
            ->once()
            ->getMock();

        $this->dispatch('/thingflokkar/100');

        $this->assertControllerName(PartyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\Party::class)
            ->shouldReceive('get')
            ->with(100)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/thingflokkar/100');

        $this->assertControllerName(PartyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $expectedData = (new Model\Party())
            ->setPartyId(100)
            ->setName('n1')
            ->setAbbrShort('p1')
            ->setAbbrLong('p2')
            ->setColor('blue');

        $this->getMockService(Service\Party::class)
            ->shouldReceive('save')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/thingflokkar/100', 'PUT', [
            'name' => 'n1',
            'abbr_short' => 'p1',
            'abbr_long' => 'p2',
            'color' => 'blue'
        ]);

        $this->assertControllerName(PartyController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new Model\Party())
            ->setPartyId(100)
            ->setName('n1')
            ->setAbbrShort('p1')
            ->setAbbrLong('p2')
            ->setColor('blue');

        $this->getMockService(Service\Party::class)
            ->shouldReceive('get')
            ->with(100)
            ->andReturn((new Model\Party())
                ->setPartyId(100)
                ->setName('n1')
                ->setAbbrShort('p1')
                ->setAbbrLong('p2')
                ->setColor('green'))
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/thingflokkar/100', 'PATCH', [
            'color' => 'blue'
        ]);

        $this->assertControllerName(PartyController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
