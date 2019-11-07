<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\Aggregate\DocumentController;
use Althingi\Service\CongressmanDocument;
use Althingi\Service\Document;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\Aggregate\DocumentController
 * @covers \Althingi\Controller\Aggregate\DocumentController::setDocumentService
 * @covers \Althingi\Controller\Aggregate\DocumentController::setCongressmanDocumentService
 */
class AggregateDocumentControllerTest extends AbstractHttpControllerTestCase
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
            CongressmanDocument::class,
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
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
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/loggjafarthing/1/thingmal/a/2/thingskjol/3', 'GET');

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
        $this->assertTrue(true);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Document::class)
            ->shouldReceive('fetchByIssue')
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/loggjafarthing/1/thingmal/a/2/thingskjol', 'GET');

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::proponentsAction
     */
    public function testProponentsAction()
    {
        $this->getMockService(CongressmanDocument::class)
            ->shouldReceive('fetchByDocument')
            ->andReturn([])
            ->once()
            ->getMock();

        $this->dispatch('/samantekt/loggjafarthing/1/thingmal/a/2/thingskjol/3/thingmenn', 'GET');

        $this->assertControllerName(DocumentController::class);
        $this->assertActionName('proponents');
        $this->assertResponseStatusCode(200);
    }
}
