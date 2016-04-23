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

class CabinetControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testSuccessGetRouter()
    {
        $pdo = Mockery::mock('PDO');
        $cabinetService = Mockery::mock('Althingi\Service\Cabinet')
            ->shouldReceive('fetchByAssembly')
            ->andReturn([])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);
        $serviceManager->setService('Althingi\Service\Cabinet', $cabinetService);

        $this->dispatch('/loggjafarthing/1/raduneyti', 'GET');

        $this->assertControllerClass('CabinetController');
        $this->assertActionName('assembly');
    }

    public function testSuccessGetHeaders()
    {
        $pdo = Mockery::mock('PDO');
        $cabinetService = Mockery::mock('Althingi\Service\Cabinet')
            ->shouldReceive('fetchByAssembly')
            ->andReturn([])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);
        $serviceManager->setService('Althingi\Service\Cabinet', $cabinetService);

        $this->dispatch('/loggjafarthing/1/raduneyti', 'GET');

        $response = $this->getResponse();

        $this->assertEquals('*', $response->getHeaders()->get('Access-Control-Allow-Origin')->getFieldValue());
        $this->assertResponseStatusCode(200);
    }

    public function testSuccessGetData()
    {
        $congressmanService = Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('fetchByCabinet')
            ->andReturn([
                (object) ['congressman_id' => 1, 'date' => date('Y-m-d H:i:s')]
            ])
            ->getMock();
        $partyService = Mockery::mock('Althingi\Service\Party')
            ->shouldReceive('getByCongressman')
            ->andReturnNull()
            ->getMock();
        $cabinetService = Mockery::mock('Althingi\Service\Cabinet')
            ->shouldReceive('fetchByAssembly')
            ->andReturn([
                (object)['cabinet_id' => 1, 'name' => 'c1', 'title' => 't1']
            ])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $congressmanService);
        $serviceManager->setService('Althingi\Service\Party', $partyService);
        $serviceManager->setService('Althingi\Service\Cabinet', $cabinetService);

        $this->dispatch('/loggjafarthing/1/raduneyti', 'GET');
    }

    public function testSuccessParams()
    {
        $pdo = Mockery::mock('PDO');
        $cabinetService = Mockery::mock('Althingi\Service\Cabinet')
            ->shouldReceive('fetchByAssembly')
            ->andReturnUsing(function ($assemblyId) {
                $this->assertEquals(1, $assemblyId);
                return [];
            })
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('PDO', $pdo);
        $serviceManager->setService('Althingi\Service\Cabinet', $cabinetService);

        $this->dispatch('/loggjafarthing/1/raduneyti', 'GET');

        $this->assertControllerClass('CabinetController');
        $this->assertActionName('assembly');
    }

}
