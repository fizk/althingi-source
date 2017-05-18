<?php

namespace Althingi\Controller;

use Althingi\Service\Plenary;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class PlenaryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\PlenaryController
 * @covers \Althingi\Controller\PlenaryController::setPlenaryService
 */
class PlenaryControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Plenary::class,
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
        $expectedData = (new \Althingi\Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(2)
            ->setName('n1')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;
        $this->getMockService(Plenary::class)
            ->shouldReceive('create')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'PUT', [
            'from' => '2001-01-01 00:00',
            'to' => '2001-01-01 00:00',
            'name' => 'n1'
        ]);

        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatchSuccess()
    {
        $expectedData = (new \Althingi\Model\Plenary())
            ->setAssemblyId(1)
            ->setPlenaryId(2)
            ->setName('newName')
            ->setFrom(new \DateTime('2001-01-01'))
            ->setTo(new \DateTime('2001-01-01'))
        ;
        $this->getMockService(Plenary::class)
            ->shouldReceive('get')
            ->andReturn(
                (new \Althingi\Model\Plenary())
                    ->setAssemblyId(1)
                    ->setPlenaryId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'))
            )
            ->once()
            ->getMock()

            ->shouldReceive('update')
            ->with(\Mockery::on(function ($actualData) use ($expectedData) {
                return $expectedData == $actualData;
            }))
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'PATCH', [
            'name' => 'newName'
        ]);

        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Plenary::class)
            ->shouldReceive('get')
            ->with(1, 2)
            ->andReturn(
                (new \Althingi\Model\Plenary())
                    ->setAssemblyId(1)
                    ->setPlenaryId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'))
            )
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'GET');
        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Plenary::class)
            ->shouldReceive('get')
            ->with(1, 2)
            ->andReturn(null)
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir/2', 'GET');
        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Plenary::class)
            ->shouldReceive('countByAssembly')
            ->andReturn(123)
            ->once()
            ->getMock()

            ->shouldReceive('fetchByAssembly')
            ->andReturn(
                [(new \Althingi\Model\Plenary())
                    ->setAssemblyId(1)
                    ->setPlenaryId(2)
                    ->setName('n1')
                    ->setFrom(new \DateTime('2001-01-01'))
                    ->setTo(new \DateTime('2001-01-01'))]
            )
            ->once()
            ->getMock();


        $this->dispatch('/loggjafarthing/1/thingfundir', 'GET');
        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Range-Unit', 'items');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-25/123');
    }
}
