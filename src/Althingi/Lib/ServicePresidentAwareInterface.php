<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/04/2016
 * Time: 11:52 AM
 */

namespace Althingi\Lib;

use Althingi\Service\President;

interface ServicePresidentAwareInterface
{
    /**
     * @param President $president
     */
    public function setPresidentService(President $president);
}
