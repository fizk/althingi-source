<?php

namespace Althingi\Controller;

use Althingi\Controller\CommitteeMeetingAgendaController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CommitteeMeetingAgendaController::class)]
#[CoversMethod(CommitteeMeetingAgendaController::class, 'setCommitteeMeetingAgendaService')]
#[CoversMethod(CommitteeMeetingAgendaController::class, 'get')]
#[CoversMethod(CommitteeMeetingAgendaController::class, 'patch')]
#[CoversMethod(CommitteeMeetingAgendaController::class, 'put')]
class CommitteeMeetingAgendaControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\CommitteeMeetingAgenda::class
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getAgendaSuccessfully()
    {
        $this->getMockService(Service\CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\CommitteeMeetingAgenda())
                ->setKind(Model\KindEnum::A)
                ->setCommitteeMeetingAgendaId(1)
                ->setCommitteeMeetingId(1646)
                ->setAssemblyId(145)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'GET', [
            'title' => 'some description'
        ]);

        $this->assertControllerName(CommitteeMeetingAgendaController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getAgendaButNotFoundError()
    {
        $this->getMockService(Service\CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'GET');

        $this->assertControllerName(CommitteeMeetingAgendaController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function putAgendaSuccessfully()
    {
        $this->getMockService(Service\CommitteeMeetingAgenda::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'PUT', [
            'title' => 'some description'
        ]);

        $this->assertControllerName(CommitteeMeetingAgendaController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function putWithInvalidArgumentsError()
    {
        $invalidId = 'ImAnInvalidId';

        $this->getMockService(Service\CommitteeMeetingAgenda::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch("/loggjafarthing/145/nefndir/202/nefndarfundir/{$invalidId}/dagskralidir/1", 'PUT');

        $this->assertControllerName(CommitteeMeetingAgendaController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchUpdateAgendaSuccessfully()
    {
        $this->getMockService(Service\CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturn(
                (new Model\CommitteeMeetingAgenda())
                ->setKind(Model\KindEnum::A)
                ->setCommitteeMeetingId(1646)
                ->setCommitteeMeetingAgendaId(1)
                ->setAssemblyId(145)
            )
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'PATCH', [
            'title' => 'some description',
            'issue_id' => 1
        ]);

        $this->assertControllerName(CommitteeMeetingAgendaController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function patchUpdateAgendaButWasNotFoundError()
    {
        $this->getMockService(Service\CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturn(null)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'PATCH', [
            'title' => 'some description',
            'issue_id' => 1
        ]);

        $this->assertControllerName(CommitteeMeetingAgendaController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
