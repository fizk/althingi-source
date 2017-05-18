<?php

namespace Althingi\Lib;

use Althingi\Service\Election;

interface ServiceElectionAwareInterface
{
    /**
     * @param Election $election
     */
    public function setElectionService(Election $election);
}
