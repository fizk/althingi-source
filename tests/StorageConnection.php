<?php

namespace AlthingiTest;

use Zumba\PHPUnit\Extensions\Mongo\Client\Connector;
use Zumba\PHPUnit\Extensions\Mongo\TestTrait;
use MongoDB\Client;

trait StorageConnection
{
    use TestTrait;

    /**
     * @var \Zumba\PHPUnit\Extensions\Mongo\Client\Connector
     */
    static protected $connection;   // phpcs:ignore

    /** @var \MongoDB\Database; */
    protected $database;

    /**
     * @return \Zumba\PHPUnit\Extensions\Mongo\Client\Connector
     */
    public function getMongoConnection()
    {
        $host = getenv('STORAGE_HOST') ?: 'store';
        $database = getenv('STORAGE_DB') ?: 'althingi';

        if (empty(self::$connection)) {
            self::$connection = new Connector(new Client("mongodb://{$host}/"));
            self::$connection->setDb($database);
        }
        $this->database = self::$connection->getConnection()->selectDatabase($database);
        return self::$connection;
    }
}
