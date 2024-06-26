<?php

namespace Althingi\Injector;

use Althingi\Service\Cabinet;

interface ServiceCabinetAwareInterface
{
    public function setCabinetService(Cabinet $cabinet): static;
}
