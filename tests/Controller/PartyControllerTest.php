<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class PartyControllerTest extends AbstractHttpControllerTestCase
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
        $serviceMock = \Mockery::mock('Althingi\Service\Party')
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals('n1', $object->name);
                $this->assertEquals('p1', $object->abbr_short);
                $this->assertEquals('p2', $object->abbr_long);
                $this->assertEquals(100, $object->party_id);
                return 10;
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Party', $serviceMock);

        $this->dispatch('/api/thingflokkar/100', 'PUT', [
            'name' => 'n1',
            'abbr_short' => 'p1',
            'abbr_long' => 'p2',
        ]);

        $this->assertControllerClass('PartyController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPutInvalidForm()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Party')
            ->shouldReceive('create')
            ->andReturn(null)
            ->never()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Party', $serviceMock);

        $this->dispatch('/api/thingflokkar/100', 'PUT', [
            'abbr_short' => 'p1',
            'abbr_long' => 'p2',
        ]);

        $this->assertControllerClass('PartyController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    public function xtestGetList()
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
