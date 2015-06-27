<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 14/06/15
 * Time: 2:51 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ConstituencyControllerTest extends AbstractHttpControllerTestCase
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
        $serviceMock = \Mockery::mock()
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals('name1', $object->name);
                $this->assertEquals(1, $object->constituency_id);
            })
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Constituency', $serviceMock);

        $this->dispatch('/api/kjordaemi/1', 'PUT', [
            'name' => 'name1'
        ]);
        $this->assertResponseStatusCode(201);
        $this->assertControllerClass('ConstituencyController');
        $this->assertActionName('put');
    }

    public function testPutInvalidForm()
    {
        $serviceMock = \Mockery::mock()
            ->shouldReceive('create')
            ->andReturn(new \stdClass())
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Constituency', $serviceMock);

        $this->dispatch('/api/kjordaemi/1', 'PUT');
        $this->assertResponseStatusCode(400);
        $this->assertControllerClass('ConstituencyController');
        $this->assertActionName('put');
    }
}
