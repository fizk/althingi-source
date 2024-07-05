<?php

namespace Althingi\Controller;

use Althingi\Controller\ParliamentarySessionController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(ParliamentarySessionController::class)]
#[CoversMethod(ParliamentarySessionController::class, 'setParliamentarySessionService')]
#[CoversMethod(ParliamentarySessionController::class, 'get')]
#[CoversMethod(ParliamentarySessionController::class, 'getList')]
#[CoversMethod(ParliamentarySessionController::class, 'patch')]
#[CoversMethod(ParliamentarySessionController::class, 'put')]
class ParliamentarySessionControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );

        $this->buildServices([
            Service\ParliamentarySession::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function putSuccess()
    {
        $expectedData = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(2)
            ->setName('n1')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;
        $this->getMockService(Service\ParliamentarySession::class)
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

        $this->assertControllerName(ParliamentarySessionController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putNegativeSuccess()
    {
        $expectedData = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(-1)
            ->setName('n1')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;
        $this->getMockService(Service\ParliamentarySession::class)
            ->shouldReceive('save')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/-1', 'PUT', [
            'from' => '2001-01-01 00:00',
            'to' => '2001-01-01 00:00',
            'name' => 'n1'
        ]);

        $this->assertControllerName(ParliamentarySessionController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function patchSuccess()
    {
        $expectedData = (new Model\ParliamentarySession())
            ->setAssemblyId(1)
            ->setParliamentarySessionId(2)
            ->setName('newName')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;
        $this->getMockService(Service\ParliamentarySession::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\ParliamentarySession())
                    ->setAssemblyId(1)
                    ->setParliamentarySessionId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'))
            )
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

        $this->assertControllerName(ParliamentarySessionController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\ParliamentarySession::class)
            ->shouldReceive('get')
            ->with(1, 2)
            ->andReturn(
                (new Model\ParliamentarySession())
                    ->setAssemblyId(1)
                    ->setParliamentarySessionId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'))
            )
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'GET');
        $this->assertControllerName(ParliamentarySessionController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\ParliamentarySession::class)
            ->shouldReceive('get')
            ->with(1, 2)
            ->andReturn(null)
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'GET');
        $this->assertControllerName(ParliamentarySessionController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\ParliamentarySession::class)
            ->shouldReceive('countByAssembly')
            ->once()
            ->andReturn(123)
            ->getMock()

            ->shouldReceive('fetchByAssembly')
            ->once()
            ->andReturn(array_map(function () {
                return (new Model\ParliamentarySession())
                    ->setAssemblyId(1)
                    ->setParliamentarySessionId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'));
            }, range(0, 24)))
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir', 'GET');
        $this->assertControllerName(ParliamentarySessionController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
