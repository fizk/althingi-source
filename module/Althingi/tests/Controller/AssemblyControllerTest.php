<?php

namespace AlthingiTest\Controller;

use Althingi\Service;
use Althingi\Model;
use Althingi\Store;
use Althingi\Controller\AssemblyController;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use DateTime;

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
 * @covers \Althingi\Controller\AssemblyController::setCongressmanService
 * @covers \Althingi\Controller\AssemblyController::setAssemblyStore
 * @covers \Althingi\Controller\AssemblyController::setIssueStore
 * @covers \Althingi\Controller\AssemblyController::setVoteStore
 * @covers \Althingi\Controller\AssemblyController::setSpeechStore
 * @covers \Althingi\Controller\AssemblyController::setPartyStore
 * @covers \Althingi\Controller\AssemblyController::setCategoryStore
 */
class AssemblyControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Service\Assembly::class,
            Service\President::class,
            Service\Issue::class,
            Service\Party::class,
            Service\Vote::class,
            Service\Speech::class,
            Service\Cabinet::class,
            Service\Category::class,
            Service\Election::class,
            Service\Congressman::class,
            Store\Assembly::class,
            Store\Issue::class,
            Store\Vote::class,
            Store\Speech::class,
            Store\Party::class,
            Store\Category::class,
            Store\Congressman::class,
            Store\Assembly::class,
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
        $this->getMockService(Store\Assembly::class)
            ->shouldReceive('get')
            ->andReturn((new Model\AssemblyProperties())
                ->setAssembly(
                    (new Model\Assembly())
                        ->setAssemblyId(144)
                        ->setFrom(new DateTime())
                ))
            ->once()
            ->getMock();

        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([
                (new Model\Cabinet())
                    ->setCabinetId(1)
                    ->setTitle('title')
            ])
            ->once()
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('fetchByCabinet')
            ->andReturn([(new Model\Party())->setPartyId(1)])
            ->once()
            ->getMock()
            ->shouldReceive('fetchByAssembly')
            ->andReturn([(new Model\Party())->setPartyId(1)])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Store\Assembly::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    /**
     * @covers ::getList
     */
    public function testGetListAll()
    {
        $this->getMockService(Store\Assembly::class)
            ->shouldReceive('fetch')
            ->andReturn([
                (new Model\AssemblyProperties())->setAssembly(
                    (new Model\Assembly())->setAssemblyId(144)->setFrom(new DateTime())
                ),
                (new Model\AssemblyProperties())->setAssembly(
                    (new Model\Assembly())->setAssemblyId(143)->setFrom(new DateTime())
                ),
                (new Model\AssemblyProperties())->setAssembly(
                    (new Model\Assembly())->setAssemblyId(144)->setFrom(new DateTime())
                ),
            ])
            ->getMock();

        $this->getMockService(Service\Cabinet::class)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([
                (new Model\Cabinet())
                    ->setCabinetId(1)
                    ->setTitle('title')
            ])
            ->times(3)
            ->getMock();

        $this->getMockService(Service\Party::class)
            ->shouldReceive('fetchByCabinet')
            ->andReturn([])
            ->times(3)
            ->shouldReceive('fetchByAssembly')
            ->andReturn([])
            ->times(3)
            ->getMock();

        $this->dispatch('/loggjafarthing', 'GET');

        $this->assertControllerName(AssemblyController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
        $this->assertResponseHeaderContains('Access-Control-Expose-Headers', 'Range-Unit, Content-Range');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-3/3');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Service\Assembly::class)
            ->shouldReceive('save')
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
        $this->getMockService(Service\Assembly::class)
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
        $assembly = (new Model\Assembly())
            ->setAssemblyId(144)
            ->setFrom(new DateTime());

        $this->getMockService(Service\Assembly::class)
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
        $this->getMockService(Service\Assembly::class)
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
        $assembly = (new Model\Assembly())
            ->setAssemblyId(144)
            ->setFrom(new DateTime('2000-01-01'));

        $this->getMockService(Service\Assembly::class)
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
        $this->getMockService(Service\Assembly::class)
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
