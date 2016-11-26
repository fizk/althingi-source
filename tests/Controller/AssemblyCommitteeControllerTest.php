<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

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
            'Althingi\Service\Committee',
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        \Mockery::close();
        return parent::tearDown();
    }

    public function testGet()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->withArgs([1])
            ->andReturn(new \stdClass())
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir/1', 'GET');

        $this->assertControllerClass('AssemblyCommitteeController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    public function testGetNotFound()
    {
        $this->getMockService('Althingi\Service\Committee')
            ->shouldReceive('get')
            ->withArgs([1])
            ->andReturnNull()
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144/nefndir/1', 'GET');

        $this->assertControllerClass('AssemblyCommitteeController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    public function testGetList()
    {
        $this->getMockService('Althingi\Service\Committee')
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
