<?php

namespace Althingi\Controller;

use Althingi\Controller\CongressmanDocumentController;
use Althingi\Model\CongressmanDocument as CongressmanDocumentModel;
use Althingi\Model\KindEnum;
use Althingi\Service\CongressmanDocument;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class CongressmanDocumentControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanDocumentController
 *
 * @covers \Althingi\Controller\CongressmanDocumentController::setCongressmanDocumentService
 */
class CongressmanDocumentControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            CongressmanDocument::class,

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
        $this->getMockService(CongressmanDocument::class)
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

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $this->getMockService(CongressmanDocument::class)
            ->shouldReceive('get')
            ->with(145, 2, 637, 1018)
            ->once()
            ->andReturn(
                (new CongressmanDocumentModel())
                    ->setAssemblyId(145)
                    ->setIssueId(2)
                    ->setKind(KindEnum::A)
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
