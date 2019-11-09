<?php

namespace AlthingiTest\Controller;

use Althingi\Service;
use Althingi\Store;
use Althingi\Model;
use Althingi\Controller;

use AlthingiTest\ServiceHelper;
use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class IssueControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\IssueController
 * @covers \Althingi\Controller\IssueController::setIssueService
 * @covers \Althingi\Controller\IssueController::setPartyService
 * @covers \Althingi\Controller\IssueController::setDocumentService
 * @covers \Althingi\Controller\IssueController::setVoteService
 * @covers \Althingi\Controller\IssueController::setAssemblyService
 * @covers \Althingi\Controller\IssueController::setSpeechService
 * @covers \Althingi\Controller\IssueController::setSearchIssueService
 * @covers \Althingi\Controller\IssueController::setIssueStore
 * @covers \Althingi\Controller\IssueController::setConstituencyService
 * @covers \Althingi\Controller\IssueController::setCategoryStore
 * @covers \Althingi\Controller\IssueController::setCategoryService
 */
class IssueControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Service\Issue::class,
            Service\Assembly::class,
            Service\Category::class,
            Store\Issue::class,
            Store\Category::class,
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::get
     * @pending mongodb test framework
     */
    public function testGetSuccessA()
    {
        $this->getMockService(Store\Issue::class)
            ->shouldReceive('get')
            ->with(100, 200, 'A')
            ->andReturn((new Model\IssueProperties())->setIssue(new Model\Issue()))
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     * @pending mongodb test framework
     */
    public function testGetSuccessB()
    {
        $this->getMockService(Store\Issue::class)
            ->shouldReceive('get')
            ->with(100, 200, 'B')
            ->andReturn((new Model\IssueProperties())->setIssue(new Model\Issue()))
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/b/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::speechTimesAction
     * @pending mongodb test framework
     */
    public function testGetSpeechTime()
    {
        $this->getMockService(Store\Issue::class)
            ->shouldReceive('fetchByAssemblyAndSpeechTime')
            ->with(100, 5, 1, ['A'])
            ->andReturn([])
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/raedutimar', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('speech-times');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::get
     * @pending mongodb test framework
     */
    public function testGetNotFound()
    {
        $this->getMockService(Store\Issue::class)
            ->shouldReceive('get')
            ->with(100, 200, 'A')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     * @pending mongodb test framework
     */
    public function testGetList()
    {
        $this->getMockService(Store\Issue::class)
            ->shouldReceive('fetchByAssembly')
            ->with(100, 0, null, [], [], ['A', 'B'])
            ->andReturn(array_map(function () {
                (new Model\IssueProperties())->setIssue(new Model\Issue());
            }, range(0, 24)))
            ->getMock()

            ->shouldReceive('countByAssembly')
            ->andReturn(123)
            ->getMock();

        $this->dispatch('/loggjafarthing/100/thingmal', 'GET');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Range-Unit', 'items');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-25/123');
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $expectedObject = (new Model\Issue())
            ->setIssueId(200)
            ->setAssemblyId(100)
            ->setCategory('A')
            ->setName('n1')
            ->setType('1')
            ->setTypeName('tn')
            ->setTypeSubname('tsn')
        ;

        $this->getMockService(Service\Issue::class)
            ->shouldReceive('save')
            ->with(Mockery::on(function ($actualObject) use ($expectedObject) {
                return $expectedObject == $actualObject;
            }))
            ->andReturn(1)
            ->once()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'PUT', [
            'name' => 'n1',
            'category' => 'c1',
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidForm()
    {
        $this->getMockService(Service\Issue::class)
            ->shouldReceive('create')
            ->never()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'PUT', [
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $expectedObject = (new Model\Issue())
            ->setIssueId(200)
            ->setAssemblyId(100)
            ->setName('n1')
            ->setCategory('c1')
            ->setType('1')
            ->setTypeName('tn')
            ->setTypeSubname('tsn')
        ;

        $this->getMockService(Service\Issue::class)
            ->shouldReceive('get')
            ->once()
            ->with(200, 100, 'A')
            ->andReturn((new Model\Issue())->setIssueId(200)->setAssemblyId(100)->setCategory('A'))
            ->getMock()

            ->shouldReceive('update')
            ->with(Mockery::on(function ($actualObject) use ($expectedObject) {
                return $expectedObject == $actualObject;
            }))
            ->andReturn(1)
            ->once()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'PATCH', [
            'name' => 'n1',
            'category' => 'c1',
            'type' => '1',
            'type_name' => 'tn',
            'type_subname' => 'tsn',
        ]);

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing/100/thingmal', 'OPTIONS');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('optionslist');
        $this->assertResponseStatusCode(200);

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/100/thingmal/a/200', 'OPTIONS');

        $this->assertControllerName(Controller\IssueController::class);
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
