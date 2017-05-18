<?php

namespace Althingi\Controller;

use Althingi\Model\Assembly as AssemblyModel;
use Althingi\Model\Cabinet as CabinetModel;
use Althingi\Service\Assembly;
use Althingi\Service\President;
use Althingi\Service\Issue;
use Althingi\Service\Party;
use Althingi\Service\Vote;
use Althingi\Service\Speech;
use Althingi\Service\Cabinet;
use Althingi\Service\Category;
use Althingi\Service\Election;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssemblyControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\AssemblyController
 * @covers \Althingi\Controller\AssemblyController::setAssemblyService
 * @covers \Althingi\Controller\AssemblyController::setIssueService
 * @covers \Althingi\Controller\AssemblyController::setPartyService
 * @covers \Althingi\Controller\AssemblyController::setSpeechService
 * @covers \Althingi\Controller\AssemblyController::setVoteService
 * @covers \Althingi\Controller\AssemblyController::setCabinetService
 * @covers \Althingi\Controller\AssemblyController::setCategoryService
 * @covers \Althingi\Controller\AssemblyController::setElectionService
 */
class AssemblyControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Assembly::class,
            President::class,
            Issue::class,
            Party::class,
            Vote::class,
            Speech::class,
            Cabinet::class,
            Category::class,
            Election::class,
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
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn((new AssemblyModel())
                ->setAssemblyId(144)
                ->setFrom(new \DateTime()))
            ->once()
            ->getMock();

        $this->getMockService(Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([
                (new CabinetModel())
                    ->setCabinetId(1)
                    ->setTitle('title')
                    ->setName('name')
            ])
            ->once()
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('fetchByCabinet')
            ->andReturn([(new \Althingi\Model\Party())->setPartyId(1)])
            ->once()
            ->getMock()
            ->shouldReceive('fetchByAssembly')
            ->andReturn([(new \Althingi\Model\Party())->setPartyId(1)])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('count')
            ->andReturn(3)
            ->once()
            ->getMock()
            ->shouldReceive('fetchAll')
            ->andReturn([
                (new AssemblyModel())->setAssemblyId(144)->setFrom(new \DateTime()),
                (new AssemblyModel())->setAssemblyId(143)->setFrom(new \DateTime()),
                (new AssemblyModel())->setAssemblyId(144)->setFrom(new \DateTime()),
            ])
            ->getMock();

        $this->getMockService(Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([
                (new CabinetModel())
                    ->setCabinetId(1)
                    ->setTitle('title')
                    ->setName('name')
            ])
            ->times(3)
            ->getMock();

        $this->getMockService(Party::class)
            ->shouldReceive('fetchByCabinet')
            ->andReturn([])
            ->times(3)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([])
            ->times(3)
            ->getMock();

        $this->dispatch('/loggjafarthing', 'GET');

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
        $this->assertResponseHeaderContains('Access-Control-Expose-Headers', 'Range-Unit, Content-Range');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-3/3');
        $this->assertResponseHeaderContains('Range-Unit', 'items');

//        print_r(json_decode($this->getResponse()->getContent()));
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
        ]);
        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidParams()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'to' => '2001-01-01',
        ]);

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $assembly = (new AssemblyModel())
            ->setAssemblyId(144)
            ->setFrom(new \DateTime());

        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn($assembly)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidParams()
    {
        $assembly = (new AssemblyModel())
            ->setAssemblyId(144)
            ->setFrom(new \DateTime('2000-01-01'));

        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn($assembly)
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => 'invalid date',
        ]);

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatchResourceNotFound()
    {
        $this->getMockService(Assembly::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '20016-01-01',
        ]);

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/loggjafarthing/144', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/loggjafarthing', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
