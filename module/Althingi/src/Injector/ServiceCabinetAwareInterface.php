<?php

namespace Althingi\Injector;

use Althingi\Service\Cabinet;

interface ServiceCabinetAwareInterface
{
    /**
     * @param Cabinet $cabinet
     */
    public function setCabinetService(Cabinet $cabinet);
}
