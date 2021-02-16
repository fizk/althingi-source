<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\VoteController;
use Althingi\Service\Vote;
use AlthingiTest\ServiceHelper;
use Mockery;
use Althingi\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Class VoteControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\VoteController
 *
 * @covers \Althingi\Controller\VoteController::setVoteService
 */
class VoteControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new ServiceManager(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Vote::class,
        ]);
    }

    public function tearDown(): void
    {
        $this->destroyServices();
        Mockery::close();
        parent::tearDown();
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

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'GET');

        $this->assertControllerName(VoteController::class);
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

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'GET');

        $this->assertControllerName(VoteController::class);
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

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur', 'GET');

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
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

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'PUT', [
            'date' => '2001-01-01 00:00:00',
            'type' => 'nei',
            'method' => 'nei',
        ]);

        $this->assertControllerName(VoteController::class);
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

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'PUT', [
            'type' => 'nei',
            'method' => 'nei',
        ]);

        $this->assertControllerName(VoteController::class);
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
            ->setCategory('A')
            ->setMethod('method');

        $expectedData = (new \Althingi\Model\Vote())
            ->setVoteId(3)
            ->setIssueId(2)
            ->setAssemblyId(1)
            ->setCategory('A')
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

        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'PATCH', [
            'date' => '2001-01-01 01:02:03',
        ]);

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur/3', 'OPTIONS');

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing/1/thingmal/a/2/atkvaedagreidslur', 'OPTIONS');

        $this->assertControllerName(VoteController::class);
        $this->assertActionName('optionsList');
        $this->assertResponseStatusCode(200);
    }
}
