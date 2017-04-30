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

/**
 * Class CommitteeMeetingAgendaControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CommitteeMeetingAgendaController
 * @covers \Althingi\Controller\CommitteeMeetingAgendaController::setCommitteeMeetingAgendaService
 */
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

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\CommitteeMeetingAgenda())
                ->setCommitteeMeetingAgendaId(1)
                ->setCommitteeMeetingId(1646)
                ->setAssemblyId(145)
            )
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'GET', [
            'title' => 'some description'
        ]);

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/nefndir/202/nefndarfundir/1646/dagskralidir/1', 'GET');

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::put
     */
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

    /**
     * @covers ::put
     */
    public function testPutInvalidArguments()
    {
        $invalidId = 'ImAnInvalidId';

        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch("/loggjafarthing/145/nefndir/202/nefndarfundir/{$invalidId}/dagskralidir/1", 'PUT');

        $this->assertControllerClass('CommitteeMeetingAgendaController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturn(
                (new \Althingi\Model\CommitteeMeetingAgenda())
                ->setCommitteeMeetingId(1646)
                ->setCommitteeMeetingAgendaId(1)
                ->setAssemblyId(145)
            )
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

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(CommitteeMeetingAgenda::class)
            ->shouldReceive('get')
            ->with(1646, 1)
            ->andReturn(null)
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
