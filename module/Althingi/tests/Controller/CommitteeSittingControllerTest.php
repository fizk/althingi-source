<?php

namespace AlthingiTest\Controller;

use Althingi\Service;
use Althingi\Model;
use Althingi\Controller;
use Althingi\Service\Session;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class SessionControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CommitteeSittingController
 * @covers \Althingi\Controller\CommitteeSittingController::setCommitteeSitting
 */
class CommitteeSittingControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Service\CommitteeSitting::class,
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
        $expectedObject = (new Model\CommitteeSitting())
            ->setCommitteeId(2)
            ->setCongressmanId(3)
            ->setAssemblyId(4)
            ->setOrder(5)
            ->setRole('role')
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/3/nefndaseta', 'POST', [
            'congressman_id' => 3,
            'committee_id' => 2,
            'assembly_id' => 4,
            'order' => 5,
            'role' => 'role',
            'from' => '2001-01-01',
            'to' => null,
        ]);

        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/nefndaseta/10');
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('post');
    }

    /**
     * @covers ::post
     */
    public function testCreateEntryAlreadyExists()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('create')
            ->andThrow(new \Exception(null, 23000))
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->andReturn(54321)
            ->once()
        ;

        $this->dispatch('/thingmenn/3/nefndaseta', 'POST', [
            'congressman_id' => 3,
            'committee_id' => 2,
            'assembly_id' => 4,
            'order' => 5,
            'role' => 'role',
            'from' => '2001-01-01',
            'to' => null,
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/nefndaseta/54321');
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('post');
    }

    /**
     * @covers ::post
     */
    public function testCreateInvalid()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('create')
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/thingmenn/3/nefndaseta', 'POST', [
            'congressman_id' => 3,
            'committee_id' => 2,
            'assembly_id' => 4,
            'order' => 5,
            'role' => 'role',
            'from' => null,
            'to' => null,
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('post');
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $serviceReturnedData = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(555)
            ->setCommitteeId(2)
            ->setCongressmanId(3)
            ->setAssemblyId(4)
            ->setOrder(5)
            ->setRole('role')
            ->setFrom(new \DateTime('2001-01-01'));

        $expectedObject = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(555)
            ->setCommitteeId(2)
            ->setCongressmanId(3)
            ->setAssemblyId(4)
            ->setOrder(5)
            ->setRole('role')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'));

        $this->getMockService(Service\CommitteeSitting::class)
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

        $this->dispatch('/thingmenn/3/nefndaseta/555', 'PATCH', [
            'to' => '2001-01-01',
        ]);

        $this->assertResponseStatusCode(205);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidParams()
    {
        $serviceReturnedData = (new Model\CommitteeSitting())
            ->setCommitteeSittingId(555)
            ->setCommitteeId(2)
            ->setCongressmanId(3)
            ->setAssemblyId(4)
            ->setOrder(5)
            ->setRole('role')
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/3/nefndaseta/555', 'PATCH', [
            'from' => 'this is not a date',
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn(null)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/2/nefndaseta/555', 'PATCH', [
            'type' => 'new type',
        ]);

        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('get')
            ->andReturn(new Model\CommitteeSitting())
            ->getMock();

        $this->dispatch('/thingmenn/2/nefndaseta/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('get');
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmenn/2/nefndaseta/1', 'GET');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\CommitteeSittingController::class);
        $this->assertActionName('get');
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Service\CommitteeSitting::class)
            ->shouldReceive('fetchByCongressman')
            ->andReturn([])
            ->getMock();

        $this->dispatch('/thingmenn/2/thingseta', 'GET');
        $this->assertControllerClass('SessionController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Content-Range', 'items 0-0/0');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
    }
}
