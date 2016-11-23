<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

require_once './module/Althingi/tests/MyAppDbUnitArrayDataSet.php';

use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\MyAppDbUnitArrayDataSet;

class IssueCategoryTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGetIssueCategory()
    {
        $service = new IssueCategory();
        $service->setDriver($this->pdo);

        $data = $service->get(145, 1, 1);

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals(145, $data->assembly_id);
        $this->assertEquals(1, $data->issue_id);
        $this->assertEquals(1, $data->category_id);
    }

    public function testCreate()
    {
        $service = new IssueCategory();
        $service->setDriver($this->pdo);

        $service->create((object) [
            'assembly_id' => 145,
            'issue_id' => 2,
            'category_id' => 34
        ]);

        $data = $service->get(145, 2, 34);
        $this->assertInstanceOf('stdClass', $data);
    }

    public function testUpdate()
    {
        $service = new IssueCategory();
        $service->setDriver($this->pdo);

        $service->update((object) [
            'assembly_id' => 145,
            'issue_id' => 1,
            'category_id' => 1
        ]);

        $data = $service->get(145, 1, 1);
        $this->assertInstanceOf('stdClass', $data);
    }

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
        return new MyAppDbUnitArrayDataSet([
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
