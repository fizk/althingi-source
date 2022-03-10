<?php

namespace Althingi\Injector;

use Althingi\Service\MinisterSitting;

interface ServiceMinisterSittingAwareInterface
{
    public function setMinisterSittingService(MinisterSitting $ministerSitting): self;
}
