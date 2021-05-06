<?php

namespace AlthingiTest\Service;

use Althingi\Service\Category;
use AlthingiTest\DatabaseConnection;
use PHPUnit\Framework\TestCase;
use Althingi\Model\Category as CategoryModel;
use Althingi\Model\CategoryAndCount as CategoryAndCountModel;
use PDO;

class CategoryTest extends TestCase
{
    use DatabaseConnection;

    private PDO $pdo;

    public function testGet()
    {
        $expectedData = (new CategoryModel())
            ->setCategoryId(1)
            ->setSuperCategoryId(1)
            ->setTitle(null)
            ->setDescription(null);

        $service = new Category();
        $service->setDriver($this->pdo);

        $actualData = $service->get(1);
        $this->assertEquals($expectedData, $actualData);
        $this->assertInstanceOf(CategoryModel::class, $actualData);
    }

    public function testFetchByAssembly()
    {
        $service = new Category();
        $service->setDriver($this->pdo);

        $categories = $service->fetchByAssembly(1);

        $this->assertCount(2, $categories);
        $this->assertInstanceOf(CategoryAndCountModel::class, $categories[0]);
    }

    public function testFetchByAssemblyAndIssue()
    {
        $service = new Category();
        $service->setDriver($this->pdo);

        $categories = $service->fetchByAssemblyAndIssue(1, 1);

        $this->assertCount(1, $categories);
        $this->assertInstanceOf(CategoryModel::class, $categories[0]);
    }

    public function testFetchByAssemblyIssueAndCategory()
    {
        $service = new Category();
        $service->setDriver($this->pdo);

        $expectedData = (new CategoryModel())
            ->setCategoryId(1)
            ->setSuperCategoryId(1)
            ->setTitle(null)
            ->setDescription(null);

        $actualCategory = $service->fetchByAssemblyIssueAndCategory(1, 1, 1);

        $this->assertEquals($expectedData, $actualCategory);
        $this->assertInstanceOf(CategoryModel::class, $actualCategory);
    }

    public function testCreate()
    {
        $category = (new CategoryModel())
            ->setCategoryId(4)
            ->setSuperCategoryId(1);

        $expectedTable = $this->createArrayDataSet([
            'Category' => [
                ['category_id' => 1, 'super_category_id' => 1, 'title' => null, 'description' => null],
                ['category_id' => 2, 'super_category_id' => 1, 'title' => 't1', 'description' => 'd1'],
                ['category_id' => 3, 'super_category_id' => 1, 'title' => 't2', 'description' => 'd2'],
                ['category_id' => 4, 'super_category_id' => 1, 'title' => null, 'description' => null],
            ],
        ])->getTable('Category');
        $actualTable = $this->getConnection()->createQueryTable('Category', 'SELECT * FROM Category');

        $service = new Category();
        $service->setDriver($this->pdo);
        $service->create($category);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testSave()
    {
        $category = (new CategoryModel())
            ->setCategoryId(4)
            ->setSuperCategoryId(1);

        $expectedTable = $this->createArrayDataSet([
            'Category' => [
                ['category_id' => 1, 'super_category_id' => 1, 'title' => null, 'description' => null],
                ['category_id' => 2, 'super_category_id' => 1, 'title' => 't1', 'description' => 'd1'],
                ['category_id' => 3, 'super_category_id' => 1, 'title' => 't2', 'description' => 'd2'],
                ['category_id' => 4, 'super_category_id' => 1, 'title' => null, 'description' => null],
            ],
        ])->getTable('Category');
        $actualTable = $this->getConnection()->createQueryTable('Category', 'SELECT * FROM Category');

        $service = new Category();
        $service->setDriver($this->pdo);
        $service->save($category);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $category = (new CategoryModel())
            ->setCategoryId(1)
            ->setSuperCategoryId(1)
            ->setTitle('Thisisanewtitle');

        $expectedTable = $this->createArrayDataSet([
            'Category' => [
                ['category_id' => 1, 'super_category_id' => 1, 'title' => 'Thisisanewtitle', 'description' => null],
                ['category_id' => 2, 'super_category_id' => 1, 'title' => 't1', 'description' => 'd1'],
                ['category_id' => 3, 'super_category_id' => 1, 'title' => 't2', 'description' => 'd2'],
            ],
        ])->getTable('Category');
        $actualTable = $this->getConnection()->createQueryTable('Category', 'SELECT * FROM Category');

        $service = new Category();
        $service->setDriver($this->pdo);
        $service->update($category);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Assembly' => [
                ['assembly_id' => 1, 'from' => '2000-01-01', 'to' => null],
                ['assembly_id' => 2, 'from' => '2000-01-01', 'to' => null],
            ],
            'Issue' => [
                ['issue_id' => 1, 'assembly_id' => 1, 'category' => 'A'],
                ['issue_id' => 2, 'assembly_id' => 1, 'category' => 'A'],
                ['issue_id' => 3, 'assembly_id' => 1, 'category' => 'A'],
                ['issue_id' => 4, 'assembly_id' => 1, 'category' => 'A'],
                ['issue_id' => 5, 'assembly_id' => 2, 'category' => 'A'],
                ['issue_id' => 6, 'assembly_id' => 2, 'category' => 'A'],
            ],
            'SuperCategory' => [
               ['super_category_id' => 1, 'title' => 'title'],
            ],
            'Category' => [
                ['category_id' => 1, 'super_category_id' => 1, 'title' => null, 'description' => null],
                ['category_id' => 2, 'super_category_id' => 1, 'title' => 't1', 'description' => 'd1'],
                ['category_id' => 3, 'super_category_id' => 1, 'title' => 't2', 'description' => 'd2'],
            ],
            'Category_has_Issue' => [
                ['category_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'category' => 'A'],
                ['category_id' => 2, 'issue_id' => 2, 'assembly_id' => 1, 'category' => 'A'],
                ['category_id' => 2, 'issue_id' => 3, 'assembly_id' => 1, 'category' => 'A'],
                ['category_id' => 2, 'issue_id' => 4, 'assembly_id' => 1, 'category' => 'A'],
                ['category_id' => 2, 'issue_id' => 5, 'assembly_id' => 2, 'category' => 'A'],
            ],
        ]);
    }
}
