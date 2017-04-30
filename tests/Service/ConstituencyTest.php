<?php

/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 11/05/2016
 * Time: 3:21 PM
 */
namespace Althingi\Service;

use PDO;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit_Extensions_Database_TestCase;
use Althingi\Model\Constituency as ConstituencyModel;

class ConstituencyTest extends PHPUnit_Extensions_Database_TestCase
{
    /** @var  \PDO */
    private $pdo;

    public function testGet()
    {
        $service = new Constituency();
        $service->setDriver($this->pdo);

        $expectedData = (new ConstituencyModel)
            ->setConstituencyId(1)
            ->setName('some-place')
            ->setAbbrShort('s-p')
            ->setAbbrLong('so-pl')
            ->setDescription('none');
        $actualData = $service->get(1);

        $this->assertEquals($expectedData, $actualData);
    }

    public function testCreate()
    {
        $constituency = (new ConstituencyModel())
            ->setName('name');

        $expectedTable = $this->createArrayDataSet([
            'Constituency' => [
                ['constituency_id' => 1, 'name' => 'some-place', 'abbr_short' => 's-p', 'abbr_long' => 'so-pl', 'description' => 'none'],
                ['constituency_id' => 2, 'name' => 'name', 'abbr_short' => null, 'abbr_long' => null, 'description' => null],
            ],
        ])->getTable('Constituency');
        $actualTable = $this->getConnection()->createQueryTable('Constituency', 'SELECT * FROM Constituency');

        $constituencyService = new Constituency();
        $constituencyService->setDriver($this->pdo);
        $constituencyService->create($constituency);

        $this->assertTablesEqual($expectedTable, $actualTable);
    }

    public function testUpdate()
    {
        $constituency = (new ConstituencyModel())
            ->setConstituencyId(1)
            ->setName('another-place');

        $expectedTable = $this->createArrayDataSet([
            'Constituency' => [
                ['constituency_id' => 1, 'name' => 'another-place', 'abbr_short' => null, 'abbr_long' => null, 'description' => null],
            ],
        ])->getTable('Constituency');
        $actualTable = $this->getConnection()->createQueryTable('Constituency', 'SELECT * FROM Constituency');

        $constituencyService = new Constituency();
        $constituencyService->setDriver($this->pdo);
        $constituencyService->update($constituency);

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
            'Constituency' => [
                ['constituency_id' => 1, 'name' => 'some-place', 'abbr_short' => 's-p', 'abbr_long' => 'so-pl', 'description' => 'none']
            ],
        ]);
    }
}
