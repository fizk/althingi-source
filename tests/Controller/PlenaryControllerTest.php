<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class PlenaryControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );
        parent::setUp();
    }

    public function testPutSuccess()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Plenary')
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals('n1', $object->name);
                $this->assertEquals(1, $object->assembly_id);
                $this->assertEquals(2, $object->plenary_id);
                return 10;
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Plenary', $serviceMock);

        $this->dispatch('/api/loggjafarthing/1/thingfundir/2', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'name' => 'n1'
        ]);

        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPutInvalidForm()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Plenary')
            ->shouldReceive('create')
            ->andReturn(null)
            ->never()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Plenary', $serviceMock);

        $this->dispatch('/api/loggjafarthing/1/thingfundir/2', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
        ]);

        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    public function testGetList()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Plenary')
            ->shouldReceive('fetchByAssembly')
                ->andReturn(array_fill(0, 25, new \stdClass()))
                ->never()
                ->getMock()
            ->shouldReceive('countByAssembly')
                ->andReturn(123)
                ->once()
                ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Plenary', $serviceMock);

        $this->dispatch('/api/loggjafarthing/1/thingfundir', 'GET');
        $this->assertControllerClass('PlenaryController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Range-Unit', 'items');
        $this->assertResponseHeaderContains('Content-Range', 'items 0-25/123');
    }
}
