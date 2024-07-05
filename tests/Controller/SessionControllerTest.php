<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\SessionController;
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(SessionController::class)]
#[CoversMethod(SessionController::class, 'setSessionService')]
#[CoversMethod(SessionController::class, 'get')]
#[CoversMethod(SessionController::class, 'getList')]
#[CoversMethod(SessionController::class, 'patch')]
#[CoversMethod(SessionController::class, 'post')]
class SessionControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Session::class,
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
        $expectedObject = (new Model\Session())
            ->setConstituencyId(1)
            ->setCongressmanId(2)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2010-01-01'))
            ->setTo(new \DateTime('2010-01-01'))
            ->setType('varamadur')
            ->setPartyId(4);

        $this->getMockService(Service\Session::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta', 'POST', [
            'constituency_id' => 1,
            'assembly_id' => 1,
            'from' => '2010-01-01',
            'to' => '2010-01-01',
            'type' => 'varamadur',
            'party_id' => 4,
        ]);

        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Location', '/thingmenn/2/thingseta/10');
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function createEntryAlreadyExists()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];

        $this->getMockService(Service\Session::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->once()
            ->andReturn(54321)
        ;

        $this->dispatch('/thingmenn/2/thingseta', 'POST', [
            'constituency_id' => 1,
            'assembly_id' => 1,
            'from' => '2010-01-01',
            'to' => '2010-01-01',
            'type' => 'varamadur',
            'party_id' => 4,
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/thingmenn/2/thingseta/54321');
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function createInvalid()
    {
        $this->getMockService(Service\Session::class)
            ->shouldReceive('create')
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta', 'POST', [
            'constituency_id' => 1,
            'from' => 'not-valid-date',
            'to' => '2010-01-01',
            'type' => 'varamadur',
            'party_id' => 2,
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function patchSuccess()
    {
        $serviceReturnedData = (new Model\Session())
            ->setSessionId(555)
            ->setConstituencyId(1)
            ->setCongressmanId(2)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2010-01-01'))
            ->setTo(new \DateTime('2010-01-01'))
            ->setType('varamadur')
            ->setPartyId(4);

        $expectedObject = (new Model\Session())
            ->setSessionId(555)
            ->setConstituencyId(1)
            ->setCongressmanId(2)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2010-01-01'))
            ->setTo(new \DateTime('2010-01-01'))
            ->setType('new type')
            ->setPartyId(4);

        $this->getMockService(Service\Session::class)
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

        $this->dispatch('/thingmenn/2/thingseta/555', 'PATCH', [
            'type' => 'new type',
        ]);

        $this->assertResponseStatusCode(205);
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchInvalidParams()
    {
        $serviceReturnedData = (new Model\Session())
            ->setSessionId(555)
            ->setConstituencyId(1)
            ->setCongressmanId(2)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2010-01-01'))
            ->setTo(new \DateTime('2010-01-01'))
            ->setType('varamadur')
            ->setPartyId(4);

        $this->getMockService(Service\Session::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/555', 'PATCH', [
            'from' => 'this is not a date',
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\Session::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn(null)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/555', 'PATCH', [
            'type' => 'new type',
        ]);

        $this->assertResponseStatusCode(404);
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\Session::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\Session())
                    ->setCongressmanId(1)
                    ->setConstituencyId(2)
                    ->setAssemblyId(4)
                    ->setFrom(new DateTime())
            )
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('get');
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\Session::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/1', 'GET');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('get');
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\Session::class)
            ->shouldReceive('fetchByCongressman')
            ->andReturn([])
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta', 'GET');
        $this->assertControllerName(SessionController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
