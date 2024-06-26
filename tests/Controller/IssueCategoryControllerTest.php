<?php

namespace Althingi\Controller;

use Althingi\Controller\IssueCategoryController;
use Althingi\Model\Category as CategoryModel;
use Althingi\Model\IssueCategory as IssueCategoryModel;
use Althingi\Model\KindEnum;
use Althingi\Service\Category;
use Althingi\Service\IssueCategory;
use Althingi\ServiceHelper;
use Library\Container\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class IssueCategoryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\IssueCategoryController
 *
 * @covers \Althingi\Controller\IssueCategoryController::setIssueCategoryService
 * @covers \Althingi\Controller\IssueCategoryController::setCategoryService
 */
class IssueCategoryControllerTest extends TestCase
{
    use ServiceHelper;

    public function setUp(): void
    {
        $this->setServiceManager(
            new Container(require __DIR__ . '/../../config/service.php')
        );
        $this->buildServices([
            Category::class,
            IssueCategory::class,
        ]);
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('fetchByAssemblyIssueAndCategory')
            ->withArgs([141, 131, 21])
            ->andReturn((new CategoryModel())->setCategoryId(21)->setSuperCategoryId(1))
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar/21', 'GET');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @covers ::get
     */
    public function testGetNotFound()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('fetchByAssemblyIssueAndCategory')
            ->withArgs([141, 131, 21])
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar/21', 'GET');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @covers ::getList
     */
    public function testList()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('fetchByAssemblyAndIssue')
            ->withArgs([141, 131])
            ->andReturn([
                (new CategoryModel())->setCategoryId(1)->setSuperCategoryId(2)
            ])
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar', 'GET');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
    }

    /**
     * @covers ::put
     */
    public function testPut()
    {
        $this->getMockService(IssueCategory::class)
            ->shouldReceive('save')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/a/131/efnisflokkar/21', 'PUT');

        $this->assertControllerName(IssueCategoryController::class);
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    /**
     * @covers ::patch
     */
    public function testPatch()
    {
        $this->getMockService(IssueCategory::class)
            ->shouldReceive('get')
            ->withArgs([141, 131, 21])
            ->andReturn(
                (new IssueCategoryModel())
                    ->setAssemblyId(141)
                    ->setIssueId(131)
                    ->setCategoryId(21)
                    ->setKind(KindEnum::A)
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

    /**
     * @covers ::patch
     */
    public function testPatchNotFound()
    {
        $this->getMockService(IssueCategory::class)
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
