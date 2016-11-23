<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class SuperCategoryControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
    }

    public function testPutSuccess()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\SuperCategory')
            ->shouldReceive('create')
            ->andReturnUsing(function ($object) {
                $this->assertEquals('1', $object->super_category_id);
                $this->assertEquals('n1', $object->title);
                return 10;
            })
            ->once()
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\SuperCategory', $serviceMock);

        $this->dispatch('/thingmal/efnisflokkar/1', 'PUT', [
            'title' => 'n1',
        ]);

        $this->assertControllerClass('SuperCategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPatchSuccess()
    {
        $serviceMock = \Mockery::mock('Althingi\Service\SuperCategory')
            ->shouldReceive('update')
            ->andReturnUsing(function ($object) {
                $this->assertEquals('1', $object->super_category_id);
                $this->assertEquals('n2', $object->title);
                return 10;
            })
            ->once()
            ->getMock()
            ->shouldReceive('get')
            ->andReturn((object) [
                'super_category_id' => 1,
                'title' => 'n1'
            ])
            ->getMock();

        $serviceManager = $this->getApplicationServiceLocator();
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Althingi\Service\SuperCategory', $serviceMock);

        $this->dispatch('/thingmal/efnisflokkar/1', 'PATCH', [
            'title' => 'n2',
        ]);

        $this->assertControllerClass('SuperCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }
}
