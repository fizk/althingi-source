<?php

namespace Althingi\Controller;

use Althingi\{Model, Service};
use Althingi\Controller\IssueCategoryController;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\Attributes\{CoversMethod, CoversClass, Test, Before, After};
use PHPUnit\Framework\TestCase;

#[CoversClass(IssueCategoryController::class)]
#[CoversMethod(IssueCategoryController::class, 'setIssueCategoryService')]
#[CoversMethod(IssueCategoryController::class, 'setCategoryService')]
#[CoversMethod(IssueCategoryController::class, 'get')]
#[CoversMethod(IssueCategoryController::class, 'getList')]
#[CoversMethod(IssueCategoryController::class, 'patch')]
#[CoversMethod(IssueCategoryController::class, 'put')]
class IssueCategoryControllerTest extends TestCase
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
            Service\IssueCategory::class,
        ]);
    }

    #[After]
    public function down(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function getSuccessful()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('fetchByAssemblyIssueAndCategory')
            ->withArgs([141, 131, 21])
            ->andReturn((new Model\Category())->setCategoryId(21)->setSuperCategoryId(1))
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar/21', 'GET');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    #[Test]
    public function getNotFound()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('fetchByAssemblyIssueAndCategory')
            ->withArgs([141, 131, 21])
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar/21', 'GET');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    #[Test]
    public function listSuccessful()
    {
        $this->getMockService(Service\Category::class)
            ->shouldReceive('fetchByAssemblyAndIssue')
            ->withArgs([141, 131])
            ->andReturn([
                (new Model\Category())->setCategoryId(1)->setSuperCategoryId(2)
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar', 'GET');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    #[Test]
    public function putSuccessful()
    {
        $this->getMockService(Service\IssueCategory::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar/21', 'PUT');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    #[Test]
    public function patchSuccessful()
    {
        $this->getMockService(Service\IssueCategory::class)
            ->shouldReceive('get')
            ->withArgs([141, 131, 21])
            ->andReturn(
                (new Model\IssueCategory())
                    ->setAssemblyId(141)
                    ->setIssueId(131)
                    ->setCategoryId(21)
                    ->setKind(Model\KindEnum::A)
            )
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar/21', 'PATCH');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    #[Test]
    public function patchNotFound()
    {
        $this->getMockService(Service\IssueCategory::class)
            ->shouldReceive('get')
            ->withArgs([141, 131, 21])
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar/21', 'PATCH');
        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
