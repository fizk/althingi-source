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
            'Althingi\Service\Assembly',
            'Althingi\Service\Issue',
            'Althingi\Service\Party',
            'Althingi\Service\Vote',
            'Althingi\Service\Speech',
            'Althingi\Service\Cabinet',
            'Althingi\Service\Category',
            'Althingi\Service\Election',
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
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturn(new \stdClass())
            ->once()
            ->getMock();

        $this->getMockService('Althingi\Service\Cabinet')
            ->shouldReceive('fetchByAssembly')
            ->andReturn([(object)['cabinet_id' => 1]])
            ->once()
            ->getMock();

        $this->getMockService('Althingi\Service\Party')
            ->shouldReceive('fetchByCabinet')
            ->andReturn([(object) ['party_id' => 1]])
            ->once()
            ->getMock()
            ->shouldReceive('fetchByAssembly')
            ->andReturn([(object) ['party_id' => 1]])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    public function testGetNotFound()
    {
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturnNull()
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
        $this->assertResponseHeaderContains('Access-Control-Allow-Origin', '*');
    }

    public function testGetList()
    {
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('count')
            ->andReturn(3)
            ->once()
            ->getMock()
            ->shouldReceive('fetchAll')
            ->andReturn(require './module/Althingi/tests/data/assemblies.php')
            ->getMock();

        $this->getMockService('Althingi\Service\Cabinet')
            ->shouldReceive('fetchByAssembly')
            ->andReturn([(object)['cabinet_id' => 1]])
            ->times(3)
            ->getMock();

        $this->getMockService('Althingi\Service\Party')
            ->shouldReceive('fetchByCabinet')
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
    }

    public function testPut()
    {
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('create')
            ->andReturn(1)
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(201);
    }

    public function testPutInvalidParams()
    {
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('create')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'to' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(400);
    }

    public function testPatch()
    {
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturn((object)require './module/Althingi/tests/data/assembly_145.php')
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(205);
    }

    public function testPatchNotFound()
    {
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturnNull()
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(404);
    }

    public function testPatchInvalidParams()
    {
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturn((object)require './module/Althingi/tests/data/assembly_145.php')
            ->once()
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->never()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => 'invalid date',
        ]);
        $this->assertResponseStatusCode(400);
    }

    public function testPatchResourceNotFound()
    {
        $this->getMockService('Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturnNull()
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '20016-01-01',
        ]);
        $this->assertResponseStatusCode(404);
    }

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
