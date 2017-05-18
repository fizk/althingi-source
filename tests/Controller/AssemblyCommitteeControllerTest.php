<?php

namespace Althingi\Controller;

use Mockery;
use Althingi\Service\Committee;
use Althingi\Model\Committee as CommitteeModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssemblyCommitteeControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\AssemblyCommitteeController
 * @covers \Althingi\Controller\AssemblyCommitteeController::setCommitteeService
 */
class AssemblyCommitteeControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
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
            ->withArgs([1])
            ->andReturn((new CommitteeModel())->setCommitteeId(1)->setFirstAssemblyId(1))
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir/1', 'GET');

        $this->assertControllerClass('AssemblyCommitteeController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Committee::class)
            ->shouldReceive('get')
            ->withArgs([1])
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir/1', 'GET');

        $this->assertControllerClass('AssemblyCommitteeController');
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
            ->shouldReceive('fetchByAssembly')
            ->withArgs([144])
            ->andReturn([])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir', 'GET');

        $this->assertControllerClass('AssemblyCommitteeController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }
}
