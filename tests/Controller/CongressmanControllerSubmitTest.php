<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 13/06/15
 * Time: 8:50 PM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CongressmanControllerSubmitTest extends AbstractHttpControllerTestCase
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
        $service = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('create')
                ->andReturnUsing(function ($item) {
                    $this->assertEquals(1, $item->congressman_id);
                    $this->assertEquals('n1', $item->name);
                    $this->assertInstanceOf('DateTime', $item->birth);
                })
                ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $service);

        $this->dispatch('/api/thingmenn/1', 'PUT', [
            'name' => 'n1',
            'birth' => '2000-01-01'
        ]);

        $this->assertResponseStatusCode(201);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('put');
    }

    public function testPutInvalid()
    {
        $service = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('create')
                ->andReturn(1)
                ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $service);

        $this->dispatch('/api/thingmenn/1', 'PUT', [
            'birth' => '2000-01-01'
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('put');
    }

    public function testPatchSuccess()
    {
        $service = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('get')
            ->andReturn((object) [
                'congressman_id' => 1,
                'name' => 'old_name',
                'birth' => '2000-01-01'
            ])
            ->getMock()
            ->shouldReceive('update')
            ->andReturnUsing(function ($object) {
                $this->assertEquals(1, $object->congressman_id);
                $this->assertEquals('new_name', $object->name);
            })
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $service);

        $this->dispatch('/api/thingmenn/1', 'PATCH', [
            'name' => 'new_name',
            'birth' => '2000-01-01'
        ]);

        $this->assertResponseStatusCode(204);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
    }

    public function testPatchResourceNotFound()
    {
        $service = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('get')
            ->andReturn(null)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $service);

        $this->dispatch('/api/thingmenn/1', 'PATCH', []);

        $this->assertResponseStatusCode(404);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
    }

    public function testPatchInvalidForm()
    {
        $service = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('get')
            ->andReturn((object) [])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $service);

        $this->dispatch('/api/thingmenn/1', 'PATCH', [
            'birth' => 'nota-a-date'
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('patch');
    }

    public function testCreateSuccess()
    {
        $service = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('create')
            ->andReturnUsing(function ($item) {
                $this->assertEquals('n1', $item->name);
                $this->assertInstanceOf('DateTime', $item->birth);

                return 12;
            })
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $service);

        $this->dispatch('/api/thingmenn', 'POST', [
            'name' => 'n1',
            'birth' => '2000-01-01'
        ]);

        $this->assertResponseStatusCode(201);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('create');
        $this->assertResponseHeaderContains('Location', '/api/thingmenn/12');
    }

    public function testCreateInvalid()
    {
        $service = \Mockery::mock('Althingi\Service\Congressman')
            ->shouldReceive('create')
            ->andReturn(null)
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\Congressman', $service);

        $this->dispatch('/api/thingmenn', 'POST', [
            'name' => 'n1',
            'birth' => 'not-valid-date'
        ]);

        $this->assertResponseStatusCode(400);
        $this->assertControllerClass('CongressmanController');
        $this->assertActionName('create');
    }
}
