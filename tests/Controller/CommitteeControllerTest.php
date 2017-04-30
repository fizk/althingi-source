<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Althingi\Service\Committee;
use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CommitteeControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CommitteeController
 * @covers \Althingi\Controller\CommitteeController::setCommitteeService
 */
class CommitteeControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../application.config.php'
        );

        parent::setUp();

        $this->buildServices([
            Committee::class,
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
        $this->getMockService(Committee::class)
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn((new \Althingi\Model\Committee()))
            ->getMock();

        $this->dispatch('/nefndir/1', 'GET');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('get')
            ->once()
            ->with(1)
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/nefndir/1', 'GET');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('fetchAll')
            ->once()
            ->andReturn([])
            ->getMock();

        $this->dispatch('/nefndir', 'GET');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PUT', [
            'first_assembly_id' => 1,
            'last_assembly_id' => 1,
            'name' => 'name',
            'abbr_short' => 'n',
            'abbr_long' => 'na',

        ]);

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidParameters()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('create')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PUT', []);

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('get')
            ->once()
            ->andReturn(
                (new \Althingi\Model\Committee())
                ->setCommitteeId(1)
                ->setFirstAssemblyId(1)
                ->setLastAssemblyId(1)
                ->setName('name')
                ->setAbbrShort('n')
                ->setAbbrLong('na')
            )
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    /**
     * @covers ::get
     */
    public function testPatchNotFound()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->once()
            ->andReturn(null)
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/nefndir/1', 'PATCH', [
            'first_assembly_id' => 1,
        ]);

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::options
     */
    public function testOptions()
    {
        $this->dispatch('/nefndir/1', 'OPTIONS');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('options');
        $this->assertResponseStatusCode(200);
        $this->assertHasResponseHeader('Allow');
        $this->assertResponseHeaderContains('Allow', 'OPTIONS, GET, PUT, PATCH');
    }

    /**
     * @covers ::optionsList
     */
    public function testOptionsList()
    {
        $this->dispatch('/nefndir', 'OPTIONS');

        $this->assertControllerClass('CommitteeController');
        $this->assertActionName('optionsList');
        $this->assertResponseStatusCode(200);
        $this->assertHasResponseHeader('Allow');
        $this->assertResponseHeaderContains('Allow', 'OPTIONS, GET');
    }
}
