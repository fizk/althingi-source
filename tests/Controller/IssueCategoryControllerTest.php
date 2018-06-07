<?php

namespace Althingi\Controller;

use Althingi\Model\Category as CategoryModel;
use Althingi\Model\IssueCategory as IssueCategoryModel;
use Althingi\Service\Category;
use Althingi\Service\IssueCategory;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class IssueCategoryControllerTest
 * @package Althingi\Controller
 * @coversDefaultClass \Althingi\Controller\IssueCategoryController
 * @covers \Althingi\Controller\IssueCategoryController::setIssueCategoryService
 * @covers \Althingi\Controller\IssueCategoryController::setCategoryService
 */
class IssueCategoryControllerTest extends AbstractHttpControllerTestCase
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
            IssueCategory::class,
        ]);
    }

    public function tearDown()
    {
        \Mockery::close();
        return parent::tearDown();
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $this->getMockService(Category::class)
            ->shouldReceive('fetchByAssemblyIssueAndCategory')
            ->withArgs([141, 131, 21])
            ->andReturn(new CategoryModel())
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'GET');
        $this->assertControllerClass('IssueCategoryController');
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

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'GET');
        $this->assertControllerClass('IssueCategoryController');
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
            ->andReturn([new CategoryModel()])
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar', 'GET');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(206);
        $this->assertResponseHeaderContains('Content-Range', 'items 0-1/1');
        $this->assertResponseHeaderContains('Range-Unit', 'items');
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

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PUT');
        $this->assertControllerClass('IssueCategoryController');
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
            ->andReturn((new IssueCategoryModel())->setAssemblyId(141)->setIssueId(131)->setCategoryId(21))
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PATCH');
        $this->assertControllerClass('IssueCategoryController');
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

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PATCH');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
