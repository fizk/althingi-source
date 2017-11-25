<?php

namespace Althingi;

use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PDO;

trait DatabaseConnection
{
    static $connection;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        $dbNameProd = getenv('DB_NAME') ?: 'althingi';
        $dbNameDev = getenv('DB_NAME_TEST') ?: 'althingi_test';
        $environment = $GLOBALS['APPLICATION_ENVIRONMENT'] === 'production' ? : 'development';
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbPort = getenv('DB_PORT') ?: 3306;
        $dbName = $environment === 'production' ? $dbNameProd : $dbNameDev;


        self::$connection = $this->pdo = self::$connection ? : new PDO(
            "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASSWORD') ?: '',
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]
        );
        return $this->createDefaultDBConnection($this->pdo);
    }
}
