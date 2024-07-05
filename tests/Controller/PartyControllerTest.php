<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\PartyController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(PartyController::class)]
#[CoversMethod(PartyController::class, 'setPartyService')]
#[CoversMethod(PartyController::class, 'get')]
class PartyControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Party::class,
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
        $this->getMockService(Service\Party::class)
            ->shouldReceive('get')
            ->with(100)
            ->andReturn((new Model\Party())->setPartyId(100)->setName('name'))
            ->once()
            ->getMock();

        $this->dispatch('/thingflokkar/100');

        $this->assertControllerName(PartyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
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

    #[Test]
    public function putSuccess()
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

    #[Test]
    public function patchSuccess()
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
