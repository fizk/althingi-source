<?php

namespace Althingi\Controller;

use Althingi\Controller\CommitteeMeetingController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CommitteeMeetingController::class)]
#[CoversMethod(CommitteeMeetingController::class, 'setCommitteeMeetingService')]
#[CoversMethod(CommitteeMeetingController::class, 'get')]
#[CoversMethod(CommitteeMeetingController::class, 'getList')]
#[CoversMethod(CommitteeMeetingController::class, 'patch')]
#[CoversMethod(CommitteeMeetingController::class, 'put')]
class CommitteeMeetingControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\CommitteeMeeting::class
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function putMeetingSuccessfully()
    {
        $this->getMockService(Service\CommitteeMeeting::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();


        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'PUT', [
            'from' => "2016-04-26 13:00:00",
            'to' => "2016-04-26 15:10:00",
            'description' => 'some description'
        ]);

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function getFetchMeetingsByGivenAssemblySuccessfully()
    {
        $this->getMockService(Service\CommitteeMeeting::class)
            ->shouldReceive('fetchByAssembly')
            ->with(145, 202)
            ->andReturn([
                (new Model\CommitteeMeeting())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1646)
                    ->setAssemblyId(145),
                (new Model\CommitteeMeeting())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1647)
                    ->setAssemblyId(145)
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir', 'GET');

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function patchUpdateMeetingSuccessfully()
    {
        $this->getMockService(Service\CommitteeMeeting::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\CommitteeMeeting())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1646)
                    ->setAssemblyId(145)
            )
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'PATCH', [
            'from' => "2016-04-26 13:00:00",
            'to' => "2016-04-26 15:10:00",
            'description' => 'some description'
        ]);

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function patchMeetingWithInvalidDateError()
    {
        $this->getMockService(Service\CommitteeMeeting::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\CommitteeMeeting())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1646)
                    ->setAssemblyId(145)
            )
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'PATCH', [
            'from' => "This is not a valid date string",
            'to' => "This is not a valid date string",
            'description' => 'some description'
        ]);

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchUpdateMeetingButWasNotFoundError()
    {
        $this->getMockService(Service\CommitteeMeeting::class)
            ->shouldReceive('get')
            ->andReturn(null)->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'PATCH', [
            'from' => "2016-04-26 13:00:00",
            'to' => "2016-04-26 15:10:00",
            'description' => 'some description'
        ]);

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function getMeetingSuccessfully()
    {
        $this->getMockService(Service\CommitteeMeeting::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\CommitteeMeeting())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1646)
                    ->setAssemblyId(145)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'GET');

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getMeetingButNotFoundError()
    {
        $this->getMockService(Service\CommitteeMeeting::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'GET');

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }
}
