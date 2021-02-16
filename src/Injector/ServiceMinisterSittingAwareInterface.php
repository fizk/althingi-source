<?php

namespace Althingi\Injector;

use Althingi\Service\MinisterSitting;

interface ServiceMinisterSittingAwareInterface
{
    /**
     * @param \Althingi\Service\MinisterSitting $ministerSitting
     */
    public function setMinisterSittingService(MinisterSitting $ministerSitting);
}
