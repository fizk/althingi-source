<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

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

    public function testPutListNotImplemented()
    {
        $this->dispatch('/api/loggjafarthing', 'PUT');
        $this->assertResponseStatusCode(405);
    }

    public function testPutSuccessful()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('create')
                ->andReturn(null)
                ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
            'to' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(201);
    }

    public function testPutParamMissing()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('create')
            ->andReturn(null)
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/144', 'PUT', [
            'to' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(400);
    }

    /**
     * @expectedException \PDOException
     * @todo fixme
     */
    public function xtestPutResourceExits()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('create')
            ->andThrow('\PDOException', 'message', 23000)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/144', 'PUT', [
            'from' => '2001-01-01',
        ]);
        $this->assertResponseStatusCode(409);
    }

    public function testGet()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturn(new \stdClass())
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/144', 'GET');
        $this->assertResponseStatusCode(200);
    }

    public function testDeleteSuccess()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('get')
                ->andReturn(new \stdClass())
                ->getMock()
            ->shouldReceive('delete')
                ->andReturn(0)
                ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/144', 'DELETE');
        $this->assertResponseStatusCode(200);
    }

    public function testDeleteNotFound()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturnNull()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/144', 'DELETE');
        $this->assertResponseStatusCode(404);
    }

    public function testGetNotFound()
    {
        $serviceMock = \Mockery::mock('\Althingi\Service\Assembly')
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/144', 'GET');
        $this->assertResponseStatusCode(404);
    }

    public function testOptions()
    {
        $this->dispatch('/api/loggjafarthing/144', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS', 'PUT', 'PATCH', 'DELETE'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }


    public function testOptionsList()
    {
        $this->dispatch('/api/loggjafarthing', 'OPTIONS');

        $expectedMethods = ['GET', 'OPTIONS'];
        $actualMethods = $this->getResponse()
            ->getHeaders()
            ->get('Allow')
            ->getAllowedMethods();

        $this->assertCount(0, array_diff($expectedMethods, $actualMethods));
    }
}
