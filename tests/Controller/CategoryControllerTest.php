<?php

namespace Althingi\Controller;

use Althingi\Controller\CategoryController;
use Althingi\{Model, Service};
use Althingi\ServiceHelper;
use Library\Container\Container;
use Mockery;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(CategoryController::class)]
#[CoversMethod(CategoryController::class, 'setCategoryService')]
#[CoversMethod(CategoryController::class, 'assemblySummaryAction')]
#[CoversMethod(CategoryController::class, 'get')]
#[CoversMethod(CategoryController::class, 'getList')]
#[CoversMethod(CategoryController::class, 'patch')]
#[CoversMethod(CategoryController::class, 'put')]
class CategoryControllerTest extends TestCase
{
    use ServiceHelper;

    #[Before]
    public function up(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Service\Category::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        Mockery::close();
        $this->destroyServices();
        parent::tearDown();
    }

    #[Test]
    public function getOneCategorySuccessfully()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('get')
            ->with(2)
            ->andReturn((new Model\Category())->setCategoryId(2)->setSuperCategoryId(1))
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2');
        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getOneCategoryWhichIsNotFound()
    {
        $this->getMockService(Service\Category::class)
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

    #[Test]
    public function fetchAllCategoriesSuccessfully()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('fetch')
            ->with(1)
            ->andReturn([
                (new Model\Category())->setCategoryId(2)->setSuperCategoryId(1)
            ])
            ->once()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar');
        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putOneCategoryWhichIsCreatedSuccessfully()
    {
        $this->getMockService(Service\Category::class)
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

    #[Test]
    public function patchOneCategorySuccessfully()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('get')
            ->once()
            ->with(2)
            ->andReturn((new Model\Category())->setCategoryId(1)->setSuperCategoryId(2))
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

    #[Test]
    public function patchOneCategoryThatDoesNotExistWithNotFoundError()
    {
        $this->getMockService(Service\Category::class)
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

    #[Test]
    public function patchOneCategoryButParametersAreMissingWithError()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('get')
            ->once()
            ->with(2)
            ->andReturn((new Model\Category())->setCategoryId(1)->setSuperCategoryId(2))
            ->getMock()
            ->shouldReceive('update')
            ->never()
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PATCH');

        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function patchOneCategoryWithError()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('create')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/thingmal/efnisflokkar/1/undirflokkar/2', 'PUT');

        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(400);
    }

    #[Test]
    public function fetchByAssembly()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('fetchByAssembly')
            ->with(123)
            ->andReturn([
                (new Model\CategoryAndCount())->setCategoryId(1)->setSuperCategoryId(123)
            ])
            ->once()
            ->getMock();

        $this->dispatch('/loggjafarthing/123/efnisflokkar');
        $this->assertControllerName(CategoryController::class);
        $this->assertActionName('assembly-summary');
        $this->assertResponseStatusCode(206);
    }
}
