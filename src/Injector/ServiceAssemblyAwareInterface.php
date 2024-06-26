<?php

namespace Althingi\Injector;

use Althingi\Service\Assembly;

interface ServiceAssemblyAwareInterface
{
    public function setAssemblyService(Assembly $assembly): static;
}
