<?php

namespace Althingi\Injector;

use \Althingi\Store\Party;

interface StorePartyAwareInterface
{
    /**
     * @param \Althingi\Store\Party $party
     */
    public function setPartyStore(Party $party);
}
