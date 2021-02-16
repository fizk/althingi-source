<?php

namespace Althingi\Injector;

use Althingi\Service\Ministry;

interface ServiceMinistryAwareInterface
{
    public function setMinistryService(Ministry $ministry): self;
}
