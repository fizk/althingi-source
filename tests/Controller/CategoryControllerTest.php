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

class CategoryControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../application.config.php'
        );
        parent::setUp();
        $this->buildServices([
            'Althingi\Service\Category',
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        $this->destroyServices();
        return parent::tearDown();
    }

    public function testPut()
    {
        $this->getMockService('Althingi\Service\Category')
            ->shouldReceive('create')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PUT', [
            'title' => 'title',
        ]);

        $this->assertControllerClass('CategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPatch()
    {
        $this->getMockService('Althingi\Service\Category')
            ->shouldReceive('get')
            ->once()
            ->with(2)
            ->andReturn((object)['category_id' => 1, 'super_category_id' => 2])
            ->getMock()
            ->shouldReceive('update')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PATCH', [
            'title' => 'title',
        ]);

        $this->assertControllerClass('CategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    public function testPatchNotFound()
    {
        $this->getMockService('Althingi\Service\Category')
            ->shouldReceive('get')
            ->once()
            ->with(2)
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PATCH', [
            'title' => 'title',
        ]);

        $this->assertControllerClass('CategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    public function testPutInvalidParams()
    {
        $this->getMockService('Althingi\Service\Category')
            ->shouldReceive('create')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PUT');

        $this->assertControllerClass('CategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }
}
