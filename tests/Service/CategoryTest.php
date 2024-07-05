<?php

namespace Althingi\Service;

use Althingi\DatabaseConnectionTrait;
use Althingi\Model;
use Althingi\Model\KindEnum;
use PHPUnit\Framework\Attributes\{Test};
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseConnectionTrait;

    #[Test]
    public function getSuccess()
    {
        $expectedData = (new Model\Category())
            ->setCategoryId(1)
            ->setSuperCategoryId(1)
            ->setTitle(null)
            ->setDescription(null);

        $service = new Category();
        $service->setDriver($this->getPDO());

        $actualData = $service->get(1);
        $this->assertEquals($expectedData, $actualData);
        $this->assertInstanceOf(Model\Category::class, $actualData);
    }

    #[Test]
    public function fetchAllGenerator()
    {
        $service = new Category();
        $service->setDriver($this->getPDO());

        $categories = [];
        foreach ($service->fetchAllGenerator() as $category) {
            $categories[] = $category;
        }

        $this->assertCount(3, $categories);
        $this->assertInstanceOf(Model\Category::class, $categories[0]);
    }

    #[Test]
    public function fetchByAssembly()
    {
        $service = new Category();
        $service->setDriver($this->getPDO());

        $categories = $service->fetchByAssembly(1);

        $this->assertCount(2, $categories);
        $this->assertInstanceOf(Model\CategoryAndCount::class, $categories[0]);
    }

    #[Test]
    public function fetchByAssemblyAndIssue()
    {
        $service = new Category();
        $service->setDriver($this->getPDO());

        $categories = $service->fetchByAssemblyAndIssue(1, 1);

        $this->assertCount(1, $categories);
        $this->assertInstanceOf(Model\Category::class, $categories[0]);
    }

    #[Test]
    public function fetchByAssemblyIssueAndCategory()
    {
        $service = new Category();
        $service->setDriver($this->getPDO());

        $expectedData = (new Model\Category())
            ->setCategoryId(1)
            ->setSuperCategoryId(1)
            ->setTitle(null)
            ->setDescription(null);

        $actualCategory = $service->fetchByAssemblyIssueAndCategory(1, 1, 1);

        $this->assertEquals($expectedData, $actualCategory);
        $this->assertInstanceOf(Model\Category::class, $actualCategory);
    }

    #[Test]
    public function createSuccess()
    {
        $category = (new Model\Category())
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
        $service->setDriver($this->getPDO());
        $service->create($category);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function saveSuccess()
    {
        $category = (new Model\Category())
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
        $service->setDriver($this->getPDO());
        $service->save($category);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    #[Test]
    public function updateSuccess()
    {
        $category = (new Model\Category())
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
        $service->setDriver($this->getPDO());
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
                ['issue_id' => 1, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['issue_id' => 2, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['issue_id' => 3, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['issue_id' => 4, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['issue_id' => 5, 'assembly_id' => 2, 'kind' => KindEnum::A->value],
                ['issue_id' => 6, 'assembly_id' => 2, 'kind' => KindEnum::A->value],
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
                ['category_id' => 1, 'issue_id' => 1, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['category_id' => 2, 'issue_id' => 2, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['category_id' => 2, 'issue_id' => 3, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['category_id' => 2, 'issue_id' => 4, 'assembly_id' => 1, 'kind' => KindEnum::A->value],
                ['category_id' => 2, 'issue_id' => 5, 'assembly_id' => 2, 'kind' => KindEnum::A->value],
            ],
        ]);
    }
}
