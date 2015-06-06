<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 19/05/15
 * Time: 1:20 PM
 */

namespace Althingi\Lib;

use \PDO;

interface DatabaseAwareInterface
{
    public function setDriver(PDO $pdo);

    public function getDriver();
}
