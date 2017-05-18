<?php

namespace Althingi\Lib;

use Althingi\Service\Party;

interface ServicePartyAwareInterface
{
    /**
     * @param Party $party
     */
    public function setPartyService(Party $party);
}
