<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 13/06/15
 * Time: 8:50 PM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CongressmanControllerGetTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testList()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $this->buildCongressmanService());
        $serviceManager->setService('PDO', \Mockery::mock('PDO'));

        $this->dispatch('/thingmenn');
        $this->assertResponseStatusCode(206);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('getList');
    }

    public function testGet()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $this->buildCongressmanService());
        $serviceManager->setService('Althingi\Service\Party', $this->buildPartyService());
        $serviceManager->setService('PDO', \Mockery::mock('PDO'));

        $this->dispatch('/thingmenn/1');
        $this->assertResponseStatusCode(200);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('get');
    }

    public function testGetNotFound()
    {
        $mock = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('get')
            ->andReturnNull()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService(
            'Althingi\Service\Congressman',
            $mock
        );
        $serviceManager->setService('PDO', \Mockery::mock('PDO'));

        $this->dispatch('/thingmenn/1');
        $this->assertResponseStatusCode(404);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('get');
    }

    public function testDelete()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $this->buildCongressmanService());
        $serviceManager->setService('PDO', \Mockery::mock('PDO'));

        $this->dispatch('/thingmenn/1', 'DELETE');
        $this->assertResponseStatusCode(205);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('delete');
    }

    private function buildPartyService()
    {
        return \Mockery::mock('Althingi\Service\Party')
            ->shouldReceive('fetchByCongressman')
            ->andReturn(null)
            ->getMock()
            ->shouldReceive('fetchAll')
            ->andReturn([])
            ->getMock();
    }

    private function buildCongressmanService()
    {
        return \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('count')
                ->andReturn(10)
                ->getMock()
            ->shouldReceive('fetchAll')
                ->andReturn(array_fill(0, 10, (object) []))
                ->getMock()
            ->shouldReceive('get')
                ->andReturn((object) [])
                ->getMock()
            ->shouldReceive('delete')
                ->andReturn(0)
                ->getMock();

    }
}
