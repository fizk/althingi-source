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
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testRouterGetList()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchColumn')
            ->andReturn(100)
            ->mock()
            ->shouldReceive('fetchAll')
            ->andReturn([])
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing', 'GET');

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('getList');
    }

    public function testGetListHeaders()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchColumn')
            ->andReturn(100)
            ->mock()
            ->shouldReceive('fetchAll')
            ->andReturn($this->assemblies())
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing', 'GET');

        $response = $this->getResponse();

        $expectedRangeHeaders = [
            'Range-Unit',
            'Content-Range'
        ];
        $responseRangeHeaders = array_map(function ($header) {
            return trim($header);
        }, explode(',', $response->getHeaders()->get('Access-Control-Expose-Headers')->getFieldValue()));


        $allowOriginHeader = $response->getHeaders()->get('Access-Control-Allow-Origin')->getFieldValue();
        $rangeUnitHeader = $response->getHeaders()->get('Range-Unit')->getFieldValue();
        $contentRangeHeader = $response->getHeaders()->get('Content-Range')->getFieldValue();
        $contentTypeHeader = $response->getHeaders()->get('Content-type')->getFieldValue();

        $this->assertEquals('*', $allowOriginHeader);
        $this->assertEquals('items', $rangeUnitHeader);
        $this->assertEquals('items 0-100/100', $contentRangeHeader);
        $this->assertEquals('application/json; charset=utf-8', $contentTypeHeader);
        $this->assertCount(0, array_diff($expectedRangeHeaders, $responseRangeHeaders));
        $this->assertResponseStatusCode(206);
    }

    public function testGetSuccess()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn($this->assembly())
            ->mock()
            ->shouldReceive('fetchAll')
            ->andReturn([])
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertResponseStatusCode(200);
    }

    public function testGetNotFound()
    {
        $assemblyServiceMock = \Mockery::mock('Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturnNull()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', \Mockery::mock('PDO'));
        $serviceManager->setService('Althingi\Service\Assembly', $assemblyServiceMock);

        $this->dispatch('/loggjafarthing/144', 'GET');
        $this->assertResponseStatusCode(404);
    }

    public function testPutListNotImplemented()
    {
        $pdoMock = Mockery::mock('\PDO');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing', 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testPutSuccessful()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(201);
    }

    public function testPutParamMissing()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144', 'PUT', [
            'to' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(400);
    }

    public function testPatchSuccessful()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn($this->assembly())
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(205);
    }

    public function testPatchUnSuccessful()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn($this->assembly())
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => 'invalid date',
        ]);
        $this->assertResponseStatusCode(400);
    }

    public function testPatchResourceNotFound()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn(false)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144', 'PATCH', [
            'from' => '20016-01-01',
        ]);
        $this->assertResponseStatusCode(404);
    }

    public function testDeleteSuccess()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn($this->assembly())
            ->mock()
            ->shouldReceive('rowCount')
            ->andReturn(1)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144', 'DELETE');
        $this->assertResponseStatusCode(200);
    }

    public function testDeleteNotFound()
    {
        $pdoMock = Mockery::mock('\PDO')
            ->shouldReceive('prepare')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('execute')
            ->andReturnSelf()
            ->mock()
            ->shouldReceive('fetchObject')
            ->andReturn(false)
            ->mock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing/144', 'DELETE');
        $this->assertResponseStatusCode(404);
    }

    public function testOptions()
    {
        $pdoMock = Mockery::mock('\PDO');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

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
        $pdoMock = Mockery::mock('\PDO');

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoMock);

        $this->dispatch('/loggjafarthing', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }

    private function assemblies()
    {
        return [
            (object)['assembly_id' => 1, 'cabinet_id' => 1, 'party_id' => 1],
            (object)['assembly_id' => 2, 'cabinet_id' => 1, 'party_id' => 1],
            (object)['assembly_id' => 3, 'cabinet_id' => 1, 'party_id' => 1],
        ];
    }

    private function assembly()
    {
        return (object)['assembly_id' => 1];
    }
}
