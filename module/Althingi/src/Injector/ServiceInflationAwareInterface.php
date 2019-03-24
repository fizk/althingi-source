<?php

namespace Althingi\Injector;

use Althingi\Service\Inflation;

interface ServiceInflationAwareInterface
{
    /**
     * @param Inflation $inflation
     */
    public function setInflationService(Inflation $inflation);
}
