<?php

namespace Althingi\Service;

use Althingi\DatabaseConnection;
use Althingi\Model\IssueCategory as IssueCategoryModel;
use Althingi\Model\IssueCategoryAndTime;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_TestCase;

class IssueCategoryTest extends PHPUnit_Extensions_Database_TestCase
{
    use DatabaseConnection;

    /** @var  \PDO */
    private $pdo;

    public function testGetIssueCategory()
    {
        $service = new IssueCategory();
        $service->setDriver($this->pdo);

        $data = $service->get(145, 1, 1);

        $this->assertInstanceOf(IssueCategoryModel::class, $data);
        $this->assertEquals(145, $data->getAssemblyId());
        $this->assertEquals(1, $data->getIssueId());
        $this->assertEquals(1, $data->getCategoryId());
    }

    public function testCreate()
    {
        $service = new IssueCategory();
        $service->setDriver($this->pdo);

        $issueCategory = (new IssueCategoryModel())
            ->setAssemblyId(145)
            ->setIssueId(2)
            ->setCategory('A')
            ->setCategoryId(34);

        $service->create($issueCategory);

        $data = $service->get(145, 2, 34);
        $this->assertEquals($issueCategory, $data);
    }

    public function testSave()
    {
        $service = new IssueCategory();
        $service->setDriver($this->pdo);

        $issueCategory = (new IssueCategoryModel())
            ->setAssemblyId(145)
            ->setIssueId(2)
            ->setCategory('A')
            ->setCategoryId(34);

        $service->save($issueCategory);

        $data = $service->get(145, 2, 34);
        $this->assertEquals($issueCategory, $data);
    }

    public function testUpdate()
    {
        $service = new IssueCategory();
        $service->setDriver($this->pdo);

        $issueCategory = (new IssueCategoryModel())
            ->setAssemblyId(145)
            ->setIssueId(1)
            ->setCategory('A')
            ->setCategoryId(1);

        $service->update($issueCategory);

        $data = $service->get(145, 1, 1);
        $this->assertEquals($issueCategory, $data);
    }

    public function testFetchFrequencyByAssemblyAndCongressman()
    {
        $service = new IssueCategory();
        $service->setDriver($this->pdo);

        $expectedData = [(new IssueCategoryAndTime())
            ->setCategoryId(1)
            ->setSuperCategoryId(1)
            ->setTime(20)
            ->setTitle('title')];

        $actualData = $service->fetchFrequencyByAssemblyAndCongressman(145, 1);
        $this->assertEquals($expectedData, $actualData);
    }

    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'Congressman' => [
                ['congressman_id' => 1, 'name' => 'name', 'birth' => '1978-04-11']
            ],
            'SuperCategory' => [
                ['super_category_id' =>  1, 'title' => 'Atvinnuvegir'],
                ['super_category_id' =>  2, 'title' => 'Hagstjórn'],
                ['super_category_id' =>  3, 'title' => 'Erlend samskipti'],
                ['super_category_id' =>  4, 'title' => 'Heilsa og heilbrigði'],
                ['super_category_id' =>  5, 'title' => 'Mennta- og menningarmál'],
                ['super_category_id' =>  6, 'title' => 'Lög og réttur'],
                ['super_category_id' =>  7, 'title' => 'Samgöngumál'],
                ['super_category_id' =>  8, 'title' => 'Stjórnarskipan og stjórnsýsla'],
                ['super_category_id' =>  9, 'title' => 'Trúmál og kirkja'],
                ['super_category_id' => 10, 'title' => 'Umhverfismál'],
                ['super_category_id' => 11, 'title' => 'Samfélagsmál'],
            ],
            'Category' => [
                ['category_id' =>  1, 'super_category_id' =>  1, 'title' => 'title'],
                ['category_id' =>  2, 'super_category_id' =>  1, 'title' => 'title'],
                ['category_id' =>  3, 'super_category_id' =>  1, 'title' => 'title'],
                ['category_id' =>  4, 'super_category_id' =>  1, 'title' => 'title'],
                ['category_id' =>  5, 'super_category_id' =>  1, 'title' => 'title'],
                ['category_id' => 34, 'super_category_id' =>  1, 'title' => 'title'],
                ['category_id' =>  6, 'super_category_id' =>  2, 'title' => 'title'],
                ['category_id' =>  7, 'super_category_id' =>  2, 'title' => 'title'],
                ['category_id' =>  8, 'super_category_id' =>  2, 'title' => 'title'],
                ['category_id' => 10, 'super_category_id' =>  3, 'title' => 'title'],
                ['category_id' => 32, 'super_category_id' =>  3, 'title' => 'title'],
                ['category_id' => 16, 'super_category_id' =>  4, 'title' => 'title'],
                ['category_id' => 17, 'super_category_id' =>  4, 'title' => 'title'],
                ['category_id' => 18, 'super_category_id' =>  5, 'title' => 'title'],
                ['category_id' => 19, 'super_category_id' =>  5, 'title' => 'title'],
                ['category_id' => 20, 'super_category_id' =>  5, 'title' => 'title'],
                ['category_id' => 21, 'super_category_id' =>  6, 'title' => 'title'],
                ['category_id' => 22, 'super_category_id' =>  6, 'title' => 'title'],
                ['category_id' => 33, 'super_category_id' =>  6, 'title' => 'title'],
                ['category_id' => 23, 'super_category_id' =>  7, 'title' => 'title'],
                ['category_id' => 24, 'super_category_id' =>  7, 'title' => 'title'],
                ['category_id' => 14, 'super_category_id' =>  8, 'title' => 'title'],
                ['category_id' => 25, 'super_category_id' =>  8, 'title' => 'title'],
                ['category_id' => 26, 'super_category_id' =>  8, 'title' => 'title'],
                ['category_id' => 27, 'super_category_id' =>  9, 'title' => 'title'],
                ['category_id' => 28, 'super_category_id' =>  9, 'title' => 'title'],
                ['category_id' => 29, 'super_category_id' => 10, 'title' => 'title'],
                ['category_id' => 30, 'super_category_id' => 10, 'title' => 'title'],
                ['category_id' => 31, 'super_category_id' => 10, 'title' => 'title'],
                ['category_id' => 11, 'super_category_id' => 11, 'title' => 'title'],
                ['category_id' => 12, 'super_category_id' => 11, 'title' => 'title'],
                ['category_id' => 13, 'super_category_id' => 11, 'title' => 'title'],
                ['category_id' => 15, 'super_category_id' => 11, 'title' => 'title'],
            ],
            'Assembly' => [[
                'assembly_id' => 145,
                'from' => '2015-09-08',
                'to' => null
            ]],
            'Plenary' => [[
                'plenary_id' => 1,
                'assembly_id' => 145,
                'from' => '2010-01-01',
                'to' => '2010-01-02',
                'name' => '',
            ]],
            'Issue' => [
                [
                    'issue_id' => 1,
                    'assembly_id' => 145,
                    'congressman_id' => null,
                    'category' => 'A',
                    'name' => '',
                    'sub_name' => '',
                    'type' => '',
                    'type_name' => '',
                    'type_subname' => '',
                    'status' => '',
                    'question' => null,
                ], [
                    'issue_id' => 2,
                    'assembly_id' => 145,
                    'congressman_id' => null,
                    'category' => 'A',
                    'name' => '',
                    'sub_name' => '',
                    'type' => '',
                    'type_name' => '',
                    'type_subname' => '',
                    'status' => '',
                    'question' => null
                ],
            ],
            'Category_has_Issue' => [
                ['category_id' => 1, 'issue_id' => 1, 'assembly_id' => 145, 'category' => 'A'],
            ],
            'Speech' => [
                [
                    'speech_id' => 'speech-id-1',
                    'plenary_id' => 1,
                    'assembly_id' => 145,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' =>  null,
                ], [
                    'speech_id' => 'speech-id-2',
                    'plenary_id' => 1,
                    'assembly_id' => 145,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => null,
                    'to' =>  '2000-01-01 00:0:10',
                ], [
                    'speech_id' => 'speech-id-3',
                    'plenary_id' => 1,
                    'assembly_id' => 145,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' =>  '2000-01-01 00:0:10',
                ], [
                    'speech_id' => 'speech-id-4',
                    'plenary_id' => 1,
                    'assembly_id' => 145,
                    'issue_id' => 1,
                    'category' => 'A',
                    'congressman_id' => 1,
                    'from' => '2000-01-01 00:00:00',
                    'to' =>  '2000-01-01 00:0:10',
                ]
            ],
        ]);
    }
}
