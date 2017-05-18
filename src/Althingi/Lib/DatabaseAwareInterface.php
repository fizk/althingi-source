<?php

namespace Althingi\Lib;

use \PDO;

interface DatabaseAwareInterface
{
    public function setDriver(PDO $pdo);

    public function getDriver();
}
