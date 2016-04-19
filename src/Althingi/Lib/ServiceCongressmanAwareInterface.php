<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/04/2016
 * Time: 11:52 AM
 */

namespace Althingi\Lib;

use Althingi\Service\Congressman;

interface ServiceCongressmanAwareInterface
{
    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman);
}
