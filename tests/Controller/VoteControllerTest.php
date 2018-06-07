<?php

namespace Althingi\Controller;

use Althingi\Service\Vote;
use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class VoteControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\VoteController
 * @covers \Althingi\Controller\VoteController::setVoteService
 */
class VoteControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Vote::class,
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Vote::class)
            ->shouldReceive('get')
            ->with(3)
            ->andReturn((new \Althingi\Model\Vote()))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3', 'GET');

        $this->assertControllerClass('VoteController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetResourceNotFound()
    {
        $this->getMockService(Vote::class)
            ->shouldReceive('get')
            ->with(3)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3', 'GET');

        $this->assertControllerClass('VoteController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Vote::class)
            ->shouldReceive('fetchByIssue')
            ->with(1, 2)
            ->andReturn([(new \Althingi\Model\Vote())])
            ->once()
            ->getMock()
        ;

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur', 'GET');

        $this->assertControllerClass('VoteController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Content-Range', 'items 0-1/1');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
    }

    /**
     * @covers ::put
     */
    public function testPutSuccess()
    {
        $this->getMockService(Vote::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3', 'PUT', [
            'date' => '2001-01-01 00:00:00',
            'type' => 'nei',
            'method' => 'nei',
        ]);

        $this->assertControllerClass('VoteController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalid()
    {
        $this->getMockService(Vote::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3', 'PUT', [
            'type' => 'nei',
            'method' => 'nei',
        ]);

        $this->assertControllerClass('VoteController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $returnedData = (new \Althingi\Model\Vote())
            ->setVoteId(3)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setDate(new \DateTime('2000-01-01 00:00:00'))
            ->setType('type')
            ->setMethod('method');

        $expectedData = (new \Althingi\Model\Vote())
            ->setVoteId(3)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setDate(new \DateTime('2001-01-01 01:02:03'))
            ->setType('type')
            ->setMethod('method');

        $this->getMockService(Vote::class)
            ->shouldReceive('get')
            ->andReturn($returnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(Mockery::on(function ($actualData) use ($expectedData) {
                return $actualData == $expectedData;
            }))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3', 'PATCH', [
            'date' => '2001-01-01 01:02:03',
        ]);

        $this->assertControllerClass('VoteController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur/3', 'OPTIONS');

        $this->assertControllerClass('VoteController');
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing/1/thingmal/2/atkvaedagreidslur', 'OPTIONS');

        $this->assertControllerClass('VoteController');
        $this->assertActionName('optionsList');
        $this->assertResponseStatusCode(200);
    }
}
