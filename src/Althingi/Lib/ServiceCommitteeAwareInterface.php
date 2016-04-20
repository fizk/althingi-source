<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/04/2016
 * Time: 11:52 AM
 */

namespace Althingi\Lib;

use Althingi\Service\Committee;

interface ServiceCommitteeAwareInterface
{
    /**
     * @param Committee $committee
     */
    public function setCommitteeService(Committee $committee);
}
