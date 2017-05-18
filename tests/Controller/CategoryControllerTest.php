<?php

namespace Althingi\Controller;

use Althingi\Service\Category;
use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CategoryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CategoryController
 * @covers \Althingi\Controller\CategoryController::setCategoryService
 */
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
            Category::class,
        ]);
    }

    public function tearDown()
    {
        Mockery::close();
        $this->destroyServices();
        return parent::tearDown();
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PUT', [
            'title' => 'title',
        ]);

        $this->assertControllerClass('CategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('get')
            ->once()
            ->with(2)
            ->andReturn((new \Althingi\Model\Category())->setCategoryId(1)->setSuperCategoryId(2))
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

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('get')
            ->once()
            ->with(2)
            ->andReturn(null)
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PATCH', [
            'title' => 'title',
        ]);

        $this->assertControllerClass('CategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::put
     */
    public function testPutInvalidParams()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PUT');

        $this->assertControllerClass('CategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }
}
