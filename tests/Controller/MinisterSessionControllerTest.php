<?php

namespace Althingi\Controller;

use Althingi\Controller;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(MinisterSessionController::class)]
#[CoversMethod(MinisterSessionController::class, 'setMinisterSessionService')]
#[CoversMethod(MinisterSessionController::class, 'assemblySessionsAction')]
#[CoversMethod(MinisterSessionController::class, 'get')]
#[CoversMethod(MinisterSessionController::class, 'patch')]
#[CoversMethod(MinisterSessionController::class, 'post')]
class MinisterSessionControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\MinisterSession::class,
            Service\Ministry::class,
            Service\Party::class,
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
    public function createSuccess()
    {
        $expectedObject = (new Model\MinisterSession())
            ->setMinistryId(1)
            ->setCongressmanId(3)
            ->setPartyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/3/radherraseta', 'POST', [
            'assembly_id' => '1',
            'ministry_id' => '1',
            'party_id' => '1',
            'from' => '2001-01-01',
        ]);

        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/radherraseta/10');
        $this->assertActionName('post');
    }

    #[Test]
    public function createEntryAlreadyExists()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];

        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->once()
            ->andReturn(54321)
            ->getMock()
        ;

        $this->dispatch('/thingmenn/3/radherraseta', 'POST', [
            'assembly_id' => '1',
            'ministry_id' => '1',
            'party_id' => '1',
            'from' => '2001-01-01',
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/radherraseta/54321');
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function createEntryAlreadyExistsVei()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];
        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->once()
            ->andReturn('54321')
            ->getMock();
        ;

        $this->dispatch('/thingmenn/3/radherraseta', 'POST', [
            'assembly_id' => '1',
            'ministry_id' => '1',
            'party_id' => '1',
            'from' => '2001-01-01',
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/radherraseta/54321');
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function createInvalid()
    {
        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('create')
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/thingmenn/3/radherraseta', 'POST', [
            'congressman_id' => 3,
            'committee_id' => 2,
            'assembly_id' => 4,
            'order' => 5,
            'role' => 'role',
            'from' => null,
            'to' => null,
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function createInvalidSteps()
    {
        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('create')
            ->andReturn(101010)
            ->getMock();

        $this->dispatch('/thingmenn/76/radherraseta', 'POST', [
            "assembly_id" => 120,
            "ministry_id" => 111,
            "party_id" => 35,
            "from" => "1995-10-02",
            "to" => "1996-09-30"
        ]);

        $this->assertResponseStatusCode(201);
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function patchSuccess()
    {
        $serviceReturnedData = (new Model\MinisterSession())
            ->setMinisterSessionId(555)
            ->setMinistryId(1)
            ->setCongressmanId(3)
            ->setPartyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
        ;

        $expectedObject = (new Model\MinisterSession())
            ->setMinisterSessionId(555)
            ->setMinistryId(1)
            ->setCongressmanId(3)
            ->setPartyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'));

        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/3/radherraseta/555', 'PATCH', [
            'to' => '2001-01-01',
        ]);

        $this->assertResponseStatusCode(205);
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchInvalidParams()
    {
        $serviceReturnedData = (new Model\MinisterSession())
            ->setMinisterSessionId(555)
            ->setMinistryId(1)
            ->setCongressmanId(3)
            ->setPartyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
        ;

        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->andReturn(10)
            ->getMock();

        $this->dispatch('/thingmenn/3/radherraseta/555', 'PATCH', [
            'to' => 'invalid date',
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn(null)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/2/radherraseta/555', 'PATCH', [
            'type' => 'new type',
        ]);

        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\MinisterSession())
                    ->setAssemblyId(1)
                    ->setMinistryId(3)
                    ->setCongressmanId(2)
            )
            ->getMock();

        $this->dispatch('/thingmenn/2/radherraseta/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('get');
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmenn/2/radherraseta/1', 'GET');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('get');
    }

    #[Test]
    public function assemblySessionsAction()
    {
        $this->getMockService(Service\MinisterSession::class)
            ->shouldReceive('fetchByCongressmanAssembly')
            ->with(123, 456)
            ->andReturn([
                (new Model\MinisterSession())
                    ->setAssemblyId(1)
                    ->setMinistryId(3)
                    ->setCongressmanId(2)
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/123/thingmenn/456/radherraseta', 'GET');

        $this->assertControllerName(Controller\MinisterSessionController::class);
        $this->assertActionName('assembly-sessions');
        $this->assertResponseStatusCode(206);
    }
}
