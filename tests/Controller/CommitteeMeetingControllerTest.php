<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\CommitteeMeetingController;
use Althingi\Service\CommitteeMeeting;
use Althingi\Model\CommitteeMeeting as CommitteeMeetingModel;
use AlthingiTest\ServiceHelper;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class CommitteeMeetingControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CommitteeMeetingController
 *
 * @covers \Althingi\Controller\CommitteeMeetingController::setCommitteeMeetingService
 */
class CommitteeMeetingControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            CommitteeMeeting::class
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
        $this->getMockService(CommitteeMeeting::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->once()
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

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(CommitteeMeeting::class)
            ->shouldReceive('fetchByAssembly')
            ->with(145, 202)
            ->andReturn([
                (new CommitteeMeetingModel())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1646)
                    ->setAssemblyId(145),
                (new CommitteeMeetingModel())
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

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $this->getMockService(CommitteeMeeting::class)
            ->shouldReceive('get')
            ->andReturn(
                (new CommitteeMeetingModel())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1646)
                    ->setAssemblyId(145)
            )->once()->getMock()
            ->shouldReceive('update')
            ->andReturn(1)->once()
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

    /**
     * @covers ::patch
     */
    public function testPatchInvalidDate()
    {
        $this->getMockService(CommitteeMeeting::class)
            ->shouldReceive('get')
            ->andReturn(
                (new CommitteeMeetingModel())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1646)
                    ->setAssemblyId(145)
            )->once()->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->never()
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

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(CommitteeMeeting::class)
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

    /**
     * @covers ::get
     */
    public function testGetSuccess()
    {
        $this->getMockService(CommitteeMeeting::class)
            ->shouldReceive('get')
            ->andReturn(
                (new CommitteeMeetingModel())
                    ->setCommitteeId(202)
                    ->setCommitteeMeetingId(1646)
                    ->setAssemblyId(145)
            )->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'GET');

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetResourceNotFound()
    {
        $this->getMockService(CommitteeMeeting::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646', 'GET');

        $this->assertControllerName(CommitteeMeetingController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }
}
