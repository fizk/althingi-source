<?php

namespace Althingi\Lib;

use Althingi\Service\Cabinet;

interface ServiceCabinetAwareInterface
{
    /**
     * @param Cabinet $cabinet
     */
    public function setCabinetService(Cabinet $cabinet);
}
