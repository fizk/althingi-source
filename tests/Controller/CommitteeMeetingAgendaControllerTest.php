<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Althingi\Service\CommitteeMeetingAgenda;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CommitteeMeetingAgendaControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            CommitteeMeetingAgenda::class
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
    }

    public function testGet()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->andReturn((object)[
                'committee_meeting_id' => 1646,
                'committee_meeting_agenda_id' => 1,
                'assembly_id' => 145
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'GET', [
            'title' => 'some description'
        ]);

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    public function testGetNotFound()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturnNull()
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'GET');

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    public function testPut()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'PUT', [
            'title' => 'some description'
        ]);

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPatch()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturn((object)[
                'committee_meeting_id' => 1646,
                'committee_meeting_agenda_id' => 1,
                'assembly_id' => 145
            ])
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'PATCH', [
            'title' => 'some description',
            'issue_id' => 1
        ]);

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    public function testPatchNotFound()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturnNull()
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'PATCH', [
            'title' => 'some description',
            'issue_id' => 1
        ]);

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
