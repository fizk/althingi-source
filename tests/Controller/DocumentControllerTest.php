<?php

namespace Althingi\Controller;

use Althingi\Controller\DocumentController;
use Althingi\Model;
use Althingi\Service;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class DocumentControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\DocumentController
 *
 * @covers \Althingi\Controller\DocumentController::setDocumentService
 */
class DocumentControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Document::class,
        ]);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('get')
            ->with(145, 2, 2)
            ->once()
            ->andReturn((new Model\Document())
                ->setDate(new \DateTime())
                ->setDocumentId(2)
                ->setIssueId(2)
                ->setCategory('category')
                ->setAssemblyId(145)
                ->setType('type')
            )
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'GET');

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('get')
            ->with(145, 2, 2)
            ->once()
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'GET');

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('fetchByIssue')
            ->with(145, 2)
            ->andReturn([
                (new Model\DocumentProperties())->setDocument(
                    (new Model\Document())
                        ->setDate(new \DateTime())
                        ->setDocumentId(2)
                        ->setIssueId(2)
                        ->setCategory('category')
                        ->setAssemblyId(145)
                        ->setType('type')
                ),
                (new Model\DocumentProperties())->setDocument(
                    (new Model\Document())
                        ->setDate(new \DateTime())
                        ->setDocumentId(2)
                        ->setIssueId(2)
                        ->setCategory('category')
                        ->setAssemblyId(145)
                        ->setType('type')

                ),
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal', 'GET');

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'PUT', [
            'date' => '2000-01-01 00:00',
            'type' => 'my-type'
        ]);

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidArgument()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('create')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'PUT', [
            'date' => 'invalid-date',
            'type' => 'my-type'
        ]);

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Document())
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

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidArguments()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Document())
                    ->setAssemblyId(145)
                    ->setIssueId(2)
                    ->setDocumentId(2)
                    ->setDate(new \DateTime())
                    ->setType('some-type')
                    ->setCategory('category')
            )
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'PATCH', [
            'date' => 'invalid-date',
            'type' => 'my-type'
        ]);

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(Service\Document::class)
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

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
