<?php

namespace Althingi\Controller;

use Althingi\Controller\DocumentController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(DocumentController::class)]
#[CoversMethod(DocumentController::class, 'setDocumentService')]
#[CoversMethod(DocumentController::class, 'get')]
#[CoversMethod(DocumentController::class, 'getList')]
#[CoversMethod(DocumentController::class, 'patch')]
#[CoversMethod(DocumentController::class, 'put')]
class DocumentControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Document::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('get')
            ->with(145, 2, 2)
            ->once()
            ->andReturn((new Model\Document())
                ->setDate(new \DateTime())
                ->setDocumentId(2)
                ->setIssueId(2)
                ->setKind(Model\KindEnum::A)
                ->setAssemblyId(145)
                ->setType('type'))
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/2', 'GET');

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
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

    #[Test]
    public function getList()
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
                        ->setKind(Model\KindEnum::A)
                        ->setAssemblyId(145)
                        ->setType('type')
                ),
                (new Model\DocumentProperties())->setDocument(
                    (new Model\Document())
                        ->setDate(new \DateTime())
                        ->setDocumentId(2)
                        ->setIssueId(2)
                        ->setKind(Model\KindEnum::A)
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

    #[Test]
    public function putSuccessful()
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

    #[Test]
    public function putInvalidArgument()
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

    #[Test]
    public function patchSuccessful()
    {
        $this->getMockService(Service\Document::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new Model\Document())
                    ->setAssemblyId(145)
                    ->setIssueId(2)
                    ->setDocumentId(2)
                    ->setKind(Model\KindEnum::A)
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

    #[Test]
    public function patchInvalidArguments()
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
                    ->setKind(Model\KindEnum::A)
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

    #[Test]
    public function patchResourceNotFound()
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
