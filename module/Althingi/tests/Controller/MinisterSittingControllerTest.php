<?php

namespace AlthingiTest\Controller;

use Althingi\Service;
use Althingi\Model;
use Althingi\Controller;
use AlthingiTest\ServiceHelper;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class MinisterSittingController
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\MinisterSittingController
 * @covers \Althingi\Controller\MinisterSittingController::setMinisterSittingService
 */
class MinisterSittingControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Service\MinisterSitting::class,
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
        $expectedObject = (new Model\MinisterSitting())
            ->setMinistryId(1)
            ->setCongressmanId(3)
            ->setPartyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2001-01-01'));

        $this->getMockService(Service\MinisterSitting::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedObject) {
                return $actualData == $expectedObject;
            }))
            ->andReturn(10)
            ->once()
            ->getMock();

        $this->dispatch('/thingmenn/3/radherraseta', 'POST', [
            'assembly_id' => '1',
            'ministry_id' => '1',
            'party_id' => '1',
            'from' => '2001-01-01',
        ]);

        $this->assertControllerName(Controller\MinisterSittingController::class);
        $this->assertResponseStatusCode(201);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/radherraseta/10');
        $this->assertActionName('post');
    }

    /**
     * @covers ::post
     */
    public function testCreateEntryAlreadyExists()
    {
        $this->getMockService(Service\MinisterSitting::class)
            ->shouldReceive('create')
            ->andThrow(new \Exception(null, 23000))
            ->once()
            ->getMock()

            ->shouldReceive('getIdentifier')
            ->andReturn(54321)
            ->once()
        ;

        $this->dispatch('/thingmenn/3/radherraseta', 'POST', [
            'assembly_id' => '1',
            'ministry_id' => '1',
            'party_id' => '1',
            'from' => '2001-01-01',
        ]);

        $this->assertResponseStatusCode(409);
        $this->assertResponseHeaderContains('Location', '/thingmenn/3/radherraseta/54321');
        $this->assertControllerName(Controller\MinisterSittingController::class);
        $this->assertActionName('post');
    }

    /**
     * @covers ::post
     */
    public function testCreateInvalid()
    {
        $this->getMockService(Service\MinisterSitting::class)
            ->shouldReceive('create')
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/thingmenn/3/radherraseta', 'POST', [
            'congressman_id' => 3,
            'committee_id' => 2,
            'assembly_id' => 4,
            'order' => 5,
            'role' => 'role',
            'from' => null,
            'to' => null,
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(Controller\MinisterSittingController::class);
        $this->assertActionName('post');
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $serviceReturnedData = (new Model\MinisterSitting())
            ->setMinisterSittingId(555)
            ->setMinistryId(1)
            ->setCongressmanId(3)
            ->setPartyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
        ;

        $expectedObject = (new Model\MinisterSitting())
            ->setMinisterSittingId(555)
            ->setMinistryId(1)
            ->setCongressmanId(3)
            ->setPartyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'));

        $this->getMockService(Service\MinisterSitting::class)
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

        $this->dispatch('/thingmenn/3/radherraseta/555', 'PATCH', [
            'to' => '2001-01-01',
        ]);

        $this->assertResponseStatusCode(205);
        $this->assertControllerName(Controller\MinisterSittingController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidParams()
    {
        $serviceReturnedData = (new Model\MinisterSitting())
            ->setMinisterSittingId(555)
            ->setMinistryId(1)
            ->setCongressmanId(3)
            ->setPartyId(1)
            ->setAssemblyId(1)
            ->setFrom(new \DateTime('2001-01-01'))
        ;

        $this->getMockService(Service\MinisterSitting::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn($serviceReturnedData)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->andReturn(10)
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/3/radherraseta/555', 'PATCH', [
            'to' => 'invalid date',
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerName(Controller\MinisterSittingController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Service\MinisterSitting::class)
            ->shouldReceive('get')
            ->with(555)
            ->andReturn(null)
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmenn/2/radherraseta/555', 'PATCH', [
            'type' => 'new type',
        ]);

        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\MinisterSittingController::class);
        $this->assertActionName('patch');
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Service\MinisterSitting::class)
            ->shouldReceive('get')
            ->andReturn(new Model\MinisterSitting())
            ->getMock();

        $this->dispatch('/thingmenn/2/radherraseta/1', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName(Controller\MinisterSittingController::class);
        $this->assertActionName('get');
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Service\MinisterSitting::class)
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/thingmenn/2/radherraseta/1', 'GET');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName(Controller\MinisterSittingController::class);
        $this->assertActionName('get');
    }
}
