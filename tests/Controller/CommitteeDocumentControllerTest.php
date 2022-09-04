<?php

namespace Althingi\Controller;

use Althingi\Service;
use Althingi\Model;
use Althingi\Controller;
use Althingi\ServiceHelper;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class SessionControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CommitteeDocumentController
 *
 * @covers \Althingi\Controller\CommitteeDocumentController::setCommitteeDocumentService
 */
class CommitteeDocumentControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\CommitteeDocument::class,
        ]);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::post
     */
    public function testCreateSuccess()
    {
        $expectedObject = (new Model\CommitteeDocument())
            ->setDocumentId(4)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setCategory('A')
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
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('post');
    }

    /**
     * @covers ::post
     */
    public function testCreateEntryAlreadyExists()
    {
        $exception = new \PDOException();
        $exception->errorInfo = ['', 1062, ''];

        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('create')
            ->andThrow($exception)
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->andReturn(54321)
            ->once()
        ;

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir', 'POST', [
            'committee_id' => 3,
            'part' => 'part',
            'name' => 'name',
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/54321');
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('post');
    }

    /**
     * @covers ::post
     */
    public function testCreateInvalid()
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
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('post');
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $serviceReturnedData = (new Model\CommitteeDocument())
            ->setDocumentCommitteeId(555)
            ->setDocumentId(4)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setCategory('A')
            ->setCommitteeId(3)
            ->setPart(null)
            ->setName(null);

        $expectedObject = (new Model\CommitteeDocument())
            ->setDocumentCommitteeId(555)
            ->setDocumentId(4)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setCategory('A')
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
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidParams()
    {
        $serviceReturnedData = (new Model\CommitteeDocument())
            ->setDocumentCommitteeId(555)
            ->setDocumentId(4)
            ->setAssemblyId(1)
            ->setIssueId(2)
            ->setCategory('A')
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
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
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
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('get')
            ->andReturn(
                (new Model\CommitteeDocument())
                    ->setDocumentId(4)
                    ->setAssemblyId(1)
                    ->setIssueId(1)
                    ->setCategory('')
                    ->setCommitteeId(1)
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/555', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('get');
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir/555', 'GET');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('get');
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Service\CommitteeDocument::class)
            ->shouldReceive('fetchByDocument')
            ->with(1, 2, 4)
            ->andReturn([
                (new Model\CommitteeDocument())
                    ->setDocumentId(4)
                    ->setAssemblyId(1)
                    ->setIssueId(1)
                    ->setCategory('')
                    ->setCommitteeId(1)
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/thingskjal/4/nefndir', 'GET');
        $this->assertResponseStatusCode(206);
        $this->assertControllerName(Controller\CommitteeDocumentController::class);
        $this->assertActionName('getList');
    }
}
