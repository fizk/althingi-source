<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 6/06/15
 * Time: 10:28 PM
 */

namespace Althingi\Controller;

use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AssemblyPatchControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
        );
        parent::setUp();
    }

    public function testPassingInArgument()
    {
        $serviceMock = \Mockery::mock()
            ->shouldReceive('get')
            ->once()
            ->andReturn((object)[
                'assembly_id' => 1,
                'from' => '2000-01-01',
                'to' => '1978-04-11',
            ])
            ->getMock()
        ->shouldReceive('update')
            ->once()
            ->andReturnUsing(function ($data) {
                $this->assertEquals('2001-01-01', $data->to);
                $this->assertEquals('2000-01-01', $data->from);
            })
        ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/1', 'PATCH', [
            'to' => '2001-01-01',
            'from' => '2000-01-01',
        ]);
        $this->assertResponseStatusCode(204);
    }

    public function testPatchResourceNotFound()
    {
        $serviceMock = \Mockery::mock()
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/1', 'PATCH');
        $this->assertResponseStatusCode(404);
    }


    public function testPatchInvalid()
    {
        $serviceMock = \Mockery::mock()
            ->shouldReceive('get')
            ->andReturn(new \stdClass())
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Assembly', $serviceMock);

        $this->dispatch('/api/loggjafarthing/1', 'PATCH', [
            'from' => 'invalid-date'
        ]);
        $this->assertResponseStatusCode(400);
    }
}
