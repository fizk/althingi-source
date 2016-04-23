<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

include __DIR__ . '/../Stubs/PDOStub.php';
include __DIR__ . '/AssemblyPdoStatementStub/GetListStatementStub.php';

use Althingi\Controller\AssemblyPdoStatementStub\GetListStatementStub;
use Althingi\Stubs\PDOStub;
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

    public function testRouterAssembliesGetList()
    {
        $pdoStub = new PDOStub('', '', '', '');
        $pdoStub->setStatement(new GetListStatementStub());

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoStub);

        $this->dispatch('/loggjafarthing', 'GET');

        $this->assertControllerClass('AssemblyController');
        $this->assertActionName('getList');
    }

    public function testAssembliesGetListHeaders()
    {
        $pdoStub = new PDOStub('', '', '', '');
        $pdoStub->setStatement(new GetListStatementStub());

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdoStub);

        $this->dispatch('/loggjafarthing', 'GET');

        $response = $this->getResponse();

        $expectedRangeHeaders = [
            'Range-Unit',
            'Content-Range'
        ];
        $responseRangeHeaders = array_map(function ($header) {
            return trim($header);
        }, explode(',', $response->getHeaders()->get('Access-Control-Expose-Headers')->getFieldValue()));

        $this->assertEquals(
            '*',
            $response->getHeaders()->get('Access-Control-Allow-Origin')->getFieldValue()
        );
        $this->assertEquals(
            'items',
            $response->getHeaders()->get('Range-Unit')->getFieldValue()
        );
        $this->assertEquals(
            'items 0-100/100',
            $response->getHeaders()->get('Content-Range')->getFieldValue()
        );
        $this->assertEquals(
            'application/json; charset=utf-8',
            $response->getHeaders()->get('Content-type')->getFieldValue()
        );
        $this->assertCount(0, array_diff($expectedRangeHeaders, $responseRangeHeaders));
        $this->assertResponseStatusCode(206);
    }

//    public function testGetSuccess()
//    {
//        $assemblyServiceMock = \Mockery::mock('\Althingi\Service\Assembly')
//            ->shouldReceive('get')
//            ->andReturn(new \stdClass())
//            ->getMock();
//
//        $issueServiceMock = \Mockery::mock('\Althingi\Service\Issue')
//            ->shouldReceive('fetchStateByAssembly')
//            ->andReturn(new \stdClass())
//            ->getMock();
//
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Assembly', $assemblyServiceMock);
//        $serviceManager->setService('Althingi\Service\Issue', $issueServiceMock);
//
//        $this->dispatch('/api/loggjafarthing/144', 'GET');
//        $this->assertResponseStatusCode(200);
//    }

//    public function testGetNotFound()
//    {
//        $assemblyServiceMock = \Mockery::mock('\Althingi\Service\Assembly')
//            ->shouldReceive('get')
//            ->andReturn(null)
//            ->getMock();
//
//        $issueServiceMock = \Mockery::mock('\Althingi\Service\Issue')
//            ->shouldReceive('fetchStateByAssembly')
//            ->andReturn(new \stdClass())
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Assembly', $assemblyServiceMock);
//        $serviceManager->setService('Althingi\Service\Issue', $issueServiceMock);
//
//        $this->dispatch('/api/loggjafarthing/144', 'GET');
//        $this->assertResponseStatusCode(404);
//    }
//
//    public function testGetListSuccess()
//    {
//        $assemblyServiceMock = \Mockery::mock('\Althingi\Service\Assembly')
//            ->shouldReceive('fetchAll')
//            ->andReturn([])
//            ->shouldReceive('count')
//            ->andReturn(0)
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Assembly', $assemblyServiceMock);
//
//        $this->dispatch('/api/loggjafarthing', 'GET');
//        $this->assertResponseStatusCode(206);
//
//    }
//
//    public function testPutListNotImplemented()
//    {
//        $this->dispatch('/api/loggjafarthing', 'PUT');
//        $this->assertResponseStatusCode(405);
//    }
//
//    public function testPutSuccessful()
//    {
//        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
//            ->shouldReceive('create')
//                ->andReturn(null)
//                ->once()
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);
//
//        $this->dispatch('/api/loggjafarthing/144', 'PUT', [
//            'from' => '2001-01-01',
//            'to' => '2001-01-01',
//        ]);
//        $this->assertResponseStatusCode(201);
//    }
//
//    public function testPutParamMissing()
//    {
//        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
//            ->shouldReceive('create')
//            ->andReturn(null)
//            ->once()
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);
//
//        $this->dispatch('/api/loggjafarthing/144', 'PUT', [
//            'to' => '2001-01-01',
//        ]);
//        $this->assertResponseStatusCode(400);
//    }
//
//    /**
//     * @expectedException \PDOException
//     * @todo fixme
//     */
//    public function XtestPutResourceExits()
//    {
//        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
//            ->shouldReceive('create')
//            ->andThrow('\PDOException', 'message', 23000)
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);
//
//        $this->dispatch('/api/loggjafarthing/144', 'PUT', [
//            'from' => '2001-01-01',
//        ]);
//        $this->assertResponseStatusCode(409);
//    }
//
//    public function testDeleteSuccess()
//    {
//        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
//            ->shouldReceive('get')
//                ->andReturn(new \stdClass())
//                ->getMock()
//            ->shouldReceive('delete')
//                ->andReturn(0)
//                ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);
//
//        $this->dispatch('/api/loggjafarthing/144', 'DELETE');
//        $this->assertResponseStatusCode(200);
//    }
//
//    public function testDeleteNotFound()
//    {
//        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
//            ->shouldReceive('get')
//            ->andReturnNull()
//            ->getMock();
//
//        $serviceManager = $this->getApplicationServiceLocator();
//        $serviceManager->setAllowOverride(true);
//        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);
//
//        $this->dispatch('/api/loggjafarthing/144', 'DELETE');
//        $this->assertResponseStatusCode(404);
//    }

//    public function testOptions()
//    {
//        $this->dispatch('/api/loggjafarthing/144', 'OPTIONS');
//
//        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'];
//        $actualMethods = $this->getResponse()
//            ->getHeaders()
//            ->get('Allow')
//            ->getAllowedMethods();
//
//        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
//    }

//    public function testOptionsList()
//    {
//        $this->dispatch('/api/loggjafarthing', 'OPTIONS');
//
//        $expectedMethods = ['GET', 'OPTIONS'];
//        $actualMethods = $this->getResponse()
//            ->getHeaders()
//            ->get('Allow')
//            ->getAllowedMethods();
//
//        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
//    }
}
