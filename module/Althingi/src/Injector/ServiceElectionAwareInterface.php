<?php

namespace Althingi\Injector;

use Althingi\Service\Election;

interface ServiceElectionAwareInterface
{
    /**
     * @param \Althingi\Service\Election $election
     */
    public function setElectionService(Election $election);
}
