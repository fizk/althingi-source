<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 9/04/2016
 * Time: 11:52 AM
 */

namespace Althingi\Lib;

use Althingi\Service\Election;

interface ServiceElectionAwareInterface
{
    /**
     * @param Election $election
     */
    public function setElectionService(Election $election);
}
