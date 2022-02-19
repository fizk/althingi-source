<?php

namespace Althingi;

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
        $dbName = getenv('DB_NAME') ?: 'althingi';
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbPort = getenv('DB_PORT') ?: 3306;

        self::$connection = $this->pdo = self::$connection ? : new PDO(
            "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
            getenv('DB_USER') ?: 'root',
            getenv('DB_PASSWORD') ?: '',
            [
                PDO::MYSQL_ATTR_INIT_COMMAND =>
                    "SET NAMES 'utf8', ".
                    "sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION,NO_AUTO_VALUE_ON_ZERO';",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]
        );
        return $this->createDefaultDBConnection($this->pdo);
    }
}
