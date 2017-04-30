<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

use Althingi\Model\IssueCategory as IssueCategoryModel;
use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;

class IssueCategoryTest extends PHPUnit_Extensions_Database_TestCase
{
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
            ->setCategoryId(34);

        $service->create($issueCategory);

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
            ->setCategoryId(1);

        $service->update($issueCategory);

        $data = $service->get(145, 1, 1);
        $this->assertEquals($issueCategory, $data);
    }

//    public function testFetchFrequencyByAssemblyAndCongressman()
//    {
//        $service = new IssueCategory();
//        $service->setDriver($this->pdo);
//
//        $service->fetchFrequencyByAssemblyAndCongressman(1, 1);
//    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        $this->pdo = new PDO(
            $GLOBALS['DB_DSN'],
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD'],
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]
        );
        return $this->createDefaultDBConnection($this->pdo);
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'SuperCategory' => require './module/Althingi/tests/data/super-categories.php',
            'Category' => require './module/Althingi/tests/data/categories.php',
            'Assembly' => [require './module/Althingi/tests/data/assembly_145.php'],
            'Plenary' => [require './module/Althingi/tests/data/plenary_145_1.php'],
            'Issue' => [
                require './module/Althingi/tests/data/issue_145_1.php',
                require './module/Althingi/tests/data/issue_145_2.php',
            ],
            'Category_has_Issue' => [
                ['category_id' => 1, 'issue_id' => 1, 'assembly_id' => 145],
            ]
        ]);
    }
}
