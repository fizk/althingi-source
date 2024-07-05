<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\CongressmanDocumentController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CongressmanDocumentController::class)]
#[CoversMethod(CongressmanDocumentController::class, 'setCongressmanDocumentService')]
#[CoversMethod(CongressmanDocumentController::class, 'put')]
#[CoversMethod(CongressmanDocumentController::class, 'patch')]
class CongressmanDocumentControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\CongressmanDocument::class,

        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function putSuccess()
    {
        $this->getMockService(Service\CongressmanDocument::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/637/flutningsmenn/1018', 'PUT', [
            'order' => '1',
        ]);

        $this->assertControllerName(CongressmanDocumentController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function patchSuccess()
    {
        $this->getMockService(Service\CongressmanDocument::class)
            ->shouldReceive('get')
            ->with(145, 2, 637, 1018)
            ->once()
            ->andReturn(
                (new Model\CongressmanDocument())
                    ->setAssemblyId(145)
                    ->setIssueId(2)
                    ->setKind(Model\KindEnum::A)
                    ->setCongressmanId(637)
                    ->setDocumentId(1018)
                    ->setOrder(1)
            )
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/a/2/thingskjal/637/flutningsmenn/1018', 'PATCH', [
            'order' => '1',
        ]);

        $this->assertControllerName(CongressmanDocumentController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
