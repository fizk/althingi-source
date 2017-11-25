<?php

namespace Althingi\Controller;

use Althingi\Service\CongressmanDocument;
use Althingi\Model\CongressmanDocument as CongressmanDocumentModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CongressmanDocumentControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CongressmanDocumentController
 * @covers \Althingi\Controller\CongressmanDocumentController::setCongressmanDocumentService
 */
class CongressmanDocumentControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            CongressmanDocument::class,

        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
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

        $this->dispatch('/loggjafarthing/145/thingmal/2/thingskjal/637/flutningsmenn/1018', 'PUT', [
            'order' => '1',
        ]);

        $this->assertControllerClass('CongressmanDocumentController');
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
                    ->setCongressmanId(637)
                    ->setDocumentId(1018)
            )
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/145/thingmal/2/thingskjal/637/flutningsmenn/1018', 'PATCH', [
            'order' => '1',
        ]);

        $this->assertControllerClass('CongressmanDocumentController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
