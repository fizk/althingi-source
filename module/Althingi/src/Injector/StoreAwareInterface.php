<?php
namespace Althingi\Injector;

use MongoDB\Database;

interface StoreAwareInterface
{
    public function setStore(Database $database);

    public function getStore(): Database;
}
