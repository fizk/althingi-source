<?php

namespace AlthingiTest\Controller;

use Althingi\Service\Congressman;
use Althingi\Service\Constituency;
use Althingi\Service\Document;
use Althingi\Service\Party;
use Althingi\Service\Vote;
use Althingi\Service\VoteItem;
use Althingi\Model\Document as DocumentModel;
use Althingi\Model\Vote as VoteModel;
use Althingi\Model\Proponent as ProponentModel;
use Althingi\Model\Party as PartyModel;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class DocumentControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\DocumentController
 *
 * @covers \Althingi\Controller\DocumentController::setDocumentService
 * @covers \Althingi\Controller\DocumentController::setCongressmanService
 * @covers \Althingi\Controller\DocumentController::setPartyService
 * @covers \Althingi\Controller\DocumentController::setVoteService
 * @covers \Althingi\Controller\DocumentController::setVoteItemService
 * @covers \Althingi\Controller\DocumentController::setConstituencyService
 * @covers \Althingi\Controller\DocumentController::setDocumentStore
 */
class DocumentControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Document::class,
            Vote::class,
            VoteItem::class,
            Congressman::class,
            Party::class,
            Constituency::class
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
        $this->getMockService(Document::class)
            ->shouldReceive('get')
            ->with(145, 2, 2)
            ->once()
            ->andReturn((new DocumentModel())->setDate(new \DateTime()))
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'GET');

        $this->assertControllerName(\Althingi\Controller\DocumentController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Document::class)
            ->shouldReceive('get')
            ->with(145, 2, 2)
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'GET');

        $this->assertControllerName(\Althingi\Controller\DocumentController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
//    public function testGetList()
//    {
//        $this->getMockService(Document::class)
//            ->shouldReceive('fetchByIssue')
//            ->with(145, 2)
//            ->once()
//            ->andReturn([
//                (new DocumentModel())->setDate(new \DateTime())->setDocumentId(1),
//                (new DocumentModel())->setDate(new \DateTime())->setDocumentId(2),
//            ])
//            ->getMock();
//
//        $this->getMockService(Vote::class)
//            ->shouldReceive('fetchByDocument')
//            ->twice()
//            ->andReturn([
//                (new VoteModel())
//            ])
//            ->getMock();
//
//        $this->getMockService(Congressman::class)
//            ->shouldReceive('fetchProponents')
//            ->twice()
//            ->andReturn([
//                (new ProponentModel())->setCongressmanId(1)
//            ])
//            ->getMock();
//
//        $this->getMockService(Party::class)
//            ->shouldReceive('getByCongressman')
//            ->andReturn(new PartyModel())
//            ->getMock();
//
//        $this->getMockService(Constituency::class)
//            ->shouldReceive('getByCongressman')
//            ->andReturn(new \Althingi\Model\ConstituencyDate())
//            ->getMock();
//
//        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal', 'GET');
//
//        $this->assertControllerName(\Althingi\Controller\DocumentController::class);
//        $this->assertActionName('getList');
//        $this->assertResponseStatusCode(206);
//        $this->assertResponseHeaderContains('Content-Range', 'items 0-2/2');
//        $this->assertResponseHeaderContains('Range-Unit', 'items');
//    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Document::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'PUT', [
            'date' => '2000-01-01 00:00',
            'type' => 'my-type'
        ]);

        $this->assertControllerName(\Althingi\Controller\DocumentController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidArgument()
    {
        $this->getMockService(Document::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'PUT', [
            'date' => 'invalid-date',
            'type' => 'my-type'
        ]);

        $this->assertControllerName(\Althingi\Controller\DocumentController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(Document::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new DocumentModel())
                    ->setAssemblyId(145)
                    ->setIssueId(2)
                    ->setDocumentId(2)
                    ->setCategory('A')
                    ->setDate(new \DateTime())
                    ->setType('some-type')
            )
            ->getMock()

            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'PATCH', [
            'date' => '2000-01-01 00:00',
            'type' => 'my-type'
        ]);

        $this->assertControllerName(\Althingi\Controller\DocumentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidArguments()
    {
        $this->getMockService(Document::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new DocumentModel())
                    ->setAssemblyId(145)
                    ->setIssueId(2)
                    ->setDocumentId(2)
                    ->setDate(new \DateTime())
                    ->setType('some-type')
            )
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'PATCH', [
            'date' => 'invalid-date',
            'type' => 'my-type'
        ]);

        $this->assertControllerName(\Althingi\Controller\DocumentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(Document::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'PATCH', [
            'date' => '2000-01-01',
            'type' => 'my-type'
        ]);

        $this->assertControllerName(\Althingi\Controller\DocumentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
