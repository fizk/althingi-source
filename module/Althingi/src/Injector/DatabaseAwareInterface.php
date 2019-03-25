<?php

namespace Althingi\Injector;

use \PDO;

interface DatabaseAwareInterface
{
    public function setDriver(PDO $pdo);

    public function getDriver();
}
