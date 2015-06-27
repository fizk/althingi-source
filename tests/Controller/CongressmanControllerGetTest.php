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
            include __DIR__ .'/../../../../config/application.config.php'
        );
        parent::setUp();
    }

    public function testList()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService(
            'Althingi\Service\Congressman',
            $this->buildService()
        );

        $this->dispatch('/api/thingmenn');
        $this->assertResponseStatusCode(206);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('getList');
    }

    public function testGet()
    {
        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService(
            'Althingi\Service\Congressman',
            $this->buildService()
        );

        $this->dispatch('/api/thingmenn/1');
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

        $this->dispatch('/api/thingmenn/1');
        $this->assertResponseStatusCode(404);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('get');
    }

    public function testDelete()
    {
        $mock = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('delete')
            ->andReturn(0)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService(
            'Althingi\Service\Congressman',
            $mock
        );

        $this->dispatch('/api/thingmenn/1', 'DELETE');
        $this->assertResponseStatusCode(204);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('delete');
    }

    private function buildService()
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
                ->getMock();

    }
}
