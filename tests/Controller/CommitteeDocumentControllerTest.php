<?php

namespace Althingi\Controller;

use Althingi\Controller\CommitteeDocumentController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CommitteeDocumentController::class)]
#[CoversMethod(CommitteeDocumentController::class, 'setCommitteeDocumentService')]
#[CoversMethod(CommitteeDocumentController::class, 'get')]
#[CoversMethod(CommitteeDocumentController::class, 'getList')]
#[CoversMethod(CommitteeDocumentController::class, 'patch')]
#[CoversMethod(CommitteeDocumentController::class, 'post')]
class CommitteeDocumentControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\CommitteeDocument::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function postCreateCommitteeDocumentSuccessfully()
    {
        $expectedObject = (new Model\CommitteeDocument())
            ->setDocumentId(4)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setKind(Model\KindEnum::A)
            ->setCommitteeId(3)
            ->setPart('part')
            ->setName('name');

        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir', 'POST', [
            'committee_id' => 3,
            'part' => 'part',
            'name' => 'name',
        ]);

        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Location', '/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/10');
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function postCreateCommitteDocumentButItAlreadyExistsError()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];

        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->once()
            ->andReturn(54321)
        ;

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir', 'POST', [
            'committee_id' => 3,
            'part' => 'part',
            'name' => 'name',
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/54321');
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function postCreateCommitteeDocumentButParamsAreIncorrectError()
    {
        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('create')
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir', 'POST', [
            'part' => 'part',
            'name' => 'name',
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('post');
    }

    #[Test]
    public function patchCommitteeDocumentSuccessfully()
    {
        $serviceReturnedData = (new Model\CommitteeDocument())
            ->setDocumentCommitteeId(555)
            ->setDocumentId(4)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setKind(Model\KindEnum::A)
            ->setCommitteeId(3)
            ->setPart(null)
            ->setName(null);

        $expectedObject = (new Model\CommitteeDocument())
            ->setDocumentCommitteeId(555)
            ->setDocumentId(4)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setKind(Model\KindEnum::A)
            ->setCommitteeId(3)
            ->setPart('part')
            ->setName('name');

        $this->getMockService(Service\CommitteeDocument::class)
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

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/555', 'PATCH', [
            'part' => 'part',
            'name' => 'name',
        ]);

        $this->assertResponseStatusCode(205);
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchCommitteeButParamsAreInvalidError()
    {
        $serviceReturnedData = (new Model\CommitteeDocument())
            ->setDocumentCommitteeId(555)
            ->setDocumentId(4)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setKind(Model\KindEnum::A)
            ->setCommitteeId(3)
            ->setPart(null)
            ->setName(null);

        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/555', 'PATCH', [
            'committee_id' => 'this is invalid',
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function patchCommitteeDocumentButTheCommitteedocumentWasNotFoundError()
    {
        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/555', 'PATCH', [
            'committee_id' => 'this is invalid',
        ]);

        $this->assertResponseStatusCode(404);
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('patch');
    }

    #[Test]
    public function getCommitteeDocumentSuccessfully()
    {
        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\CommitteeDocument())
                    ->setDocumentId(4)
                    ->setAssemblyId(1)
                    ->setIssueId(1)
                    ->setKind(Model\KindEnum::A)
                    ->setCommitteeId(1)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/555', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('get');
    }

    #[Test]
    public function getCommitteeUnsuccessfullyError()
    {
        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/555', 'GET');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('get');
    }

    #[Test]
    public function fetchByDocumentDocumentSuccess()
    {
        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('fetchByDocument')
            ->with(1, 2, 4)
            ->andReturn([
                (new Model\CommitteeDocument())
                    ->setDocumentId(4)
                    ->setAssemblyId(1)
                    ->setIssueId(1)
                    ->setKind(Model\KindEnum::A)
                    ->setCommitteeId(1)
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir', 'GET');
        $this->assertResponseStatusCode(206);
        $this->assertControllerName(CommitteeDocumentController::class);
        $this->assertActionName('getList');
    }
}
