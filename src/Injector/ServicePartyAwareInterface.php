<?php

namespace Althingi\Injector;

use Althingi\Service\Party;

interface ServicePartyAwareInterface
{
    public function setPartyService(Party $party): self;
}
