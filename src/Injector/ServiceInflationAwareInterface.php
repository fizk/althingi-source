<?php

namespace Althingi\Injector;

use Althingi\Service\Inflation;

interface ServiceInflationAwareInterface
{
    public function setInflationService(Inflation $inflation): self;
}
