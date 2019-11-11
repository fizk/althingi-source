<?php

namespace Althingi\Injector;

use Althingi\Service\Ministry;

interface ServiceMinistryAwareInterface
{
    /**
     * @param \Althingi\Service\Ministry $ministry
     * @return $this
     */
    public function setMinistryService(Ministry $ministry);
}
