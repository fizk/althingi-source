<?php

namespace Althingi\Lib;

use Althingi\Service\Election;

interface ServiceElectionAwareInterface
{
    /**
     * @param \Althingi\Service\Election $election
     */
    public function setElectionService(Election $election);
}
