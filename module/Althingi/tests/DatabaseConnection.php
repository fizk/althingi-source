<?php

namespace AlthingiTest;

//use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use PHPUnit\DbUnit\TestCaseTrait;
use PDO;

trait DatabaseConnection
{
    use TestCaseTrait;

    static protected $connection;   // phpcs:ignore

    /**
     * Returns the test database connection.
     *

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
                PDO::MYSQL_ATTR_INIT_COMMAND =>
                    "SET NAMES 'utf8', ".
                    "sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]
        );
        return $this->createDefaultDBConnection($this->pdo);
    }
}
