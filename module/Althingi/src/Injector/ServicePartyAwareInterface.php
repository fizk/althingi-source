<?php

namespace Althingi\Injector;

use Althingi\Service\Party;

interface ServicePartyAwareInterface
{
    /**
     * @param \Althingi\Service\Party $party
     */
    public function setPartyService(Party $party);
}
