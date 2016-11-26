<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 20/05/15
 * Time: 7:40 AM
 */

namespace Althingi\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

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
            'Althingi\Service\Category',
            'Althingi\Service\IssueCategory'
        ]);
    }

    public function tearDown()
    {
        $this->destroyServices();
        return parent::tearDown();
    }

    public function testGet()
    {
        $this->getMockService('Althingi\Service\Category')
            ->shouldReceive('fetchByAssemblyIssueAndCategory')
            ->withArgs([141, 131, 21])
            ->andReturn(require './module/Althingi/tests/data/category.php')
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'GET');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(200);
    }

    public function testGetNotFound()
    {
        $this->getMockService('Althingi\Service\Category')
            ->shouldReceive('fetchByAssemblyIssueAndCategory')
            ->withArgs([141, 131, 21])
            ->andReturn(null)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'GET');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('get');
        $this->assertResponseStatusCode(404);
    }

    public function testList()
    {
        $this->getMockService('Althingi\Service\Category')
            ->shouldReceive('fetchByAssemblyAndIssue')
            ->withArgs([141, 131])
            ->andReturn(require './module/Althingi/tests/data/categories.php')
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar', 'GET');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('getList');
        $this->assertResponseStatusCode(200);
    }

    public function testPut()
    {
        $this->getMockService('Althingi\Service\IssueCategory')
            ->shouldReceive('create')
            ->andReturn()
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PUT');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('put');
        $this->assertResponseStatusCode(201);
    }

    public function testPatch()
    {
        $this->getMockService('Althingi\Service\IssueCategory')
            ->shouldReceive('get')
            ->withArgs([141, 131, 21])
            ->andReturn((object) ['assembly_id' => 141, 'issue_id' => 131, 'category_id' => 21])
            ->getMock()
            ->shouldReceive('update')
            ->andReturn(1)
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PATCH');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(205);
    }

    public function testPatchNotFound()
    {
        $this->getMockService('Althingi\Service\IssueCategory')
            ->shouldReceive('get')
            ->withArgs([141, 131, 21])
            ->andReturnNull()
            ->getMock();

        $this->dispatch('/loggjafarthing/141/thingmal/131/efnisflokkar/21', 'PATCH');
        $this->assertControllerClass('IssueCategoryController');
        $this->assertActionName('patch');
        $this->assertResponseStatusCode(404);
    }
}
