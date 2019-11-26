<?php

namespace AlthingiTest\Controller;

use Althingi\Controller\CategoryController;
use Althingi\Service\Category;
use Althingi\Model;
use AlthingiTest\ServiceHelper;
use Mockery;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class CategoryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\CategoryController
 *
 * @covers \Althingi\Controller\CategoryController::setCategoryService
 */
class CategoryControllerTest extends AbstractHttpControllerTestCase
{
    use ServiceHelper;

    public function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ .'/../../../../config/application.config.php'
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
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('get')
            ->with(2)
            ->andReturn(new Model\Category())
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2');
        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('get')
            ->with(2)
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2');
        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testGetList()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('fetch')
            ->with(1)
            ->andReturn([new Model\Category()])
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar');
        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('save')
            ->once()
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PUT', [
            'title' => 'title',
        ]);

        $this->assertControllerName(CategoryController::class);
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

        $this->assertControllerName(CategoryController::class);
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

        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::patch
     */
    public function testPatchInvalidFormValues()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('get')
            ->once()
            ->with(2)
            ->andReturn((new \Althingi\Model\Category())->setCategoryId(1)->setSuperCategoryId(2))
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PATCH');

        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
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

        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    /**
     * @covers ::assemblySummaryAction
     */
    public function testAssemblySummaryAction()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('fetchByAssembly')
            ->with(123)
            ->andReturn([new Model\CategoryAndCount()])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/123/efnisflokkar');
        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('assembly-summary');
        $this->assertResponseStatusCode(206);
    }
}
