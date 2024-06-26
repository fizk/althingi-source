<?php

namespace Althingi\Injector;

use Althingi\Service\President;

interface ServicePresidentAwareInterface
{
    public function setPresidentService(President $president): static;
}
