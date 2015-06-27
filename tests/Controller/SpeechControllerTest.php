<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class SpeechControllerTest extends AbstractHttpControllerTestCase
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
        $serviceMock = \Mockery::mock('Althingi\Service\Session')
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals(20, $object->plenary_id);
                $this->assertEquals(10, $object->congressman_id);
                $this->assertEquals('*', $object->iteration);
                $this->assertEquals(1, $object->assembly_id);
                $this->assertEquals(3, $object->issue_id);
                $this->assertEquals(4, $object->speech_id);
                return 10;
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $serviceMock);

        $this->dispatch('/api/loggjafarthing/1/thingmal/3/raedur/4', 'PUT', [
            'from' => '2001-01-01 00:00:00',
            'to' => '2001-01-01 00:00:00',
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPutInvalidForm()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\Session')
            ->shouldReceive('create')
            ->andReturn(null)
            ->never()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Speech', $serviceMock);

        $this->dispatch('/api/loggjafarthing/1/thingmal/3/raedur/4', 'PUT', [
            'plenary_id' => 20,
            'congressman_id' => 10,
            'congressman_type' => null,
            'iteration' => '*',
            'type' => 't1',
            'text' => 't2'
        ]);

        $this->assertControllerClass('SpeechController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }
}
