<?php

namespace Althingi\Controller;

use Althingi\Service\Session;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class SessionControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\SessionController
 * @covers \Althingi\Controller\SessionController::setSessionService
 */
class SessionControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Session::class,
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::post
     */
    public function testCreateSuccess()
    {
        $expectedObject = (new \Althingi\Model\Session())
            ->setConstituencyId(1)
            ->setCongressmanId(2)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2010-01-01'))
            ->setTo(new \DateTime('2010-01-01'))
            ->setType('varamadur')
            ->setPartyId(4);

        $this->getMockService(Session::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta', 'POST', [
            'constituency_id' => 1,
            'assembly_id' => 1,
            'from' => '2010-01-01',
            'to' => '2010-01-01',
            'type' => 'varamadur',
            'party_id' => 4,
        ]);

        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Location', '/thingmenn/2/thingseta/10');
        $this->assertControllerClass('SessionController');
        $this->assertActionName('post');
    }

    /**
     * @covers ::post
     */
    public function testCreateEntryAlreadyExists()
    {
        $this->getMockService(Session::class)
            ->shouldReceive('create')
            ->andThrow(new \Exception(null, 23000))
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->andReturn(54321)
            ->once()
        ;

        $this->dispatch('/thingmenn/2/thingseta', 'POST', [
            'constituency_id' => 1,
            'assembly_id' => 1,
            'from' => '2010-01-01',
            'to' => '2010-01-01',
            'type' => 'varamadur',
            'party_id' => 4,
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/thingmenn/2/thingseta/54321');
        $this->assertControllerClass('SessionController');
        $this->assertActionName('post');
    }

    /**
     * @covers ::post
     */
    public function testCreateInvalid()
    {
        $this->getMockService(Session::class)
            ->shouldReceive('create')
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta', 'POST', [
            'constituency_id' => 1,
            'from' => 'not-valid-date',
            'to' => '2010-01-01',
            'type' => 'varamadur',
            'party_id' => 2,
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerClass('SessionController');
        $this->assertActionName('post');
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $serviceReturnedData = (new \Althingi\Model\Session())
            ->setSessionId(555)
            ->setConstituencyId(1)
            ->setCongressmanId(2)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2010-01-01'))
            ->setTo(new \DateTime('2010-01-01'))
            ->setType('varamadur')
            ->setPartyId(4);

        $expectedObject = (new \Althingi\Model\Session())
            ->setSessionId(555)
            ->setConstituencyId(1)
            ->setCongressmanId(2)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2010-01-01'))
            ->setTo(new \DateTime('2010-01-01'))
            ->setType('new type')
            ->setPartyId(4);

        $this->getMockService(Session::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/555', 'PATCH', [
            'type' => 'new type',
        ]);

        $this->assertResponseStatusCode(205);
        $this->assertControllerClass('SessionController');
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidParams()
    {
        $serviceReturnedData = (new \Althingi\Model\Session())
            ->setSessionId(555)
            ->setConstituencyId(1)
            ->setCongressmanId(2)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2010-01-01'))
            ->setTo(new \DateTime('2010-01-01'))
            ->setType('varamadur')
            ->setPartyId(4);

        $this->getMockService(Session::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/555', 'PATCH', [
            'from' => 'this is not a date',
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerClass('SessionController');
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Session::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn(null)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/555', 'PATCH', [
            'type' => 'new type',
        ]);

        $this->assertResponseStatusCode(404);
        $this->assertControllerClass('SessionController');
        $this->assertActionName('patch');
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Session::class)
            ->shouldReceive('get')
            ->andReturn(new \Althingi\Model\Session())
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerClass('SessionController');
        $this->assertActionName('get');
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Session::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta/1', 'GET');
        $this->assertResponseStatusCode(404);
        $this->assertControllerClass('SessionController');
        $this->assertActionName('get');
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Session::class)
            ->shouldReceive('fetchByCongressman')
            ->andReturn([])
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerClass('SessionController');
        $this->assertActionName('getList');
    }
}
