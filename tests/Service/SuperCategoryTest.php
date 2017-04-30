<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

use Althingi\Model\SuperCategory as SuperCategoryModel;
use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;

class SuperCategoryTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $service = new SuperCategory();
        $service->setDriver($this->pdo);

        $expectedData = (new SuperCategoryModel())
            ->setSuperCategoryId(1)
            ->setTitle('title1');
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $superCategory = (new SuperCategoryModel())
            ->setSuperCategoryId(10)
            ->setTitle('MyTitle');

        $expectedTable = $this->createArrayDataSet([
            'SuperCategory' => [
                ['super_category_id' => 10, 'title' => 'MyTitle'],
            ],
        ])->getTable('SuperCategory');
        $actualTable = $this->getConnection()
            ->createQueryTable('SuperCategory', 'SELECT * FROM SuperCategory where `super_category_id` = 10');

        $service = new SuperCategory();
        $service->setDriver($this->pdo);
        $service->create($superCategory);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $superCategory = (new SuperCategoryModel())
            ->setSuperCategoryId(1)
            ->setTitle('MyTitle');

        $expectedTable = $this->createArrayDataSet([
            'SuperCategory' => [
                ['super_category_id' => 1, 'title' => 'MyTitle'],
            ],
        ])->getTable('SuperCategory');
        $actualTable = $this->getConnection()
            ->createQueryTable('SuperCategory', 'SELECT * FROM SuperCategory where `super_category_id` = 1');

        $service = new SuperCategory();
        $service->setDriver($this->pdo);
        $service->update($superCategory);

        $this->assertTablesEqual($expectedTable, $actualTable);
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
        return $this->createArrayDataSet([
            'SuperCategory' => [
                ['super_category_id' => 1, 'title' => 'title1'],
                ['super_category_id' => 2, 'title' => 'title2'],
                ['super_category_id' => 3, 'title' => 'title3'],
            ],
        ]);
    }
}
