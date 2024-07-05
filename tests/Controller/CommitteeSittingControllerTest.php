<?php

namespace Althingi\Controller;

use Althingi\Controller;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use DateTime;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CommitteeSittingController::class)]
#[CoversMethod(CommitteeSittingController::class, 'setCommitteeSitting')]
#[CoversMethod(CommitteeSittingController::class, 'get')]
#[CoversMethod(CommitteeSittingController::class, 'getList')]
#[CoversMethod(CommitteeSittingController::class, 'patch')]
#[CoversMethod(CommitteeSittingController::class, 'post')]
class CommitteeSittingControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\CommitteeSitting::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function postCreateSuccess()
    {
        $expectedObject = (new Model\CommitteeSitting())
            ->setCommitteeId(2)
            ->setCongressmanId(3)
            ->setAssemblyId(4)
            ->setOrder(5)
            ->setRole('role')
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/3/nefndaseta', 'POST', [
            'congressman_id' => 3,
            'committee_id' => 2,
            'assembly_id' => 4,
            'order' => 5,
            'role' => 'role',
            'from' => '2001-01-01',
            'to' => null,
        ]);

        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/nefndaseta/10');
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function postCreateEntryAlreadyExists()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];

        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->once()
            ->andReturn(54321)
        ;

        $this->dispatch('/thingmenn/3/nefndaseta', 'POST', [
            'congressman_id' => 3,
            'committee_id' => 2,
            'assembly_id' => 4,
            'order' => 5,
            'role' => 'role',
            'from' => '2001-01-01',
            'to' => null,
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/nefndaseta/54321');
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function postCreateInvalid()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('create')
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/thingmenn/3/nefndaseta', 'POST', [
            'congressman_id' => 3,
            'committee_id' => 2,
            'assembly_id' => 4,
            'order' => 5,
            'role' => 'role',
            'from' => null,
            'to' => null,
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function patchSuccess()
    {
        $serviceReturnedData = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(555)
            ->setCommitteeId(2)
            ->setCongressmanId(3)
            ->setAssemblyId(4)
            ->setOrder(5)
            ->setRole('role')
            ->setFrom(new \DateTime('2001-01-01'));

        $expectedObject = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(555)
            ->setCommitteeId(2)
            ->setCongressmanId(3)
            ->setAssemblyId(4)
            ->setOrder(5)
            ->setRole('role')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'));

        $this->getMockService(Service\CommitteeSitting::class)
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

        $this->dispatch('/thingmenn/3/nefndaseta/555', 'PATCH', [
            'to' => '2001-01-01',
        ]);

        $this->assertResponseStatusCode(205);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchInvalidParams()
    {
        $serviceReturnedData = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(555)
            ->setCommitteeId(2)
            ->setCongressmanId(3)
            ->setAssemblyId(4)
            ->setOrder(5)
            ->setRole('role')
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/3/nefndaseta/555', 'PATCH', [
            'from' => 'this is not a date',
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn(null)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/2/nefndaseta/555', 'PATCH', [
            'type' => 'new type',
        ]);

        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function getSuccessfull()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('get')
            ->andReturn((new Model\CommitteeSitting())->setFrom(new DateTime()))
            ->getMock();

        $this->dispatch('/thingmenn/2/nefndaseta/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('get');
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmenn/2/nefndaseta/1', 'GET');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('get');
    }

    #[Test]
    public function getList()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('fetchByCongressman')
            ->andReturn([])
            ->getMock();

        $this->dispatch('/thingmenn/2/nefndaseta', 'GET');
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }
}
