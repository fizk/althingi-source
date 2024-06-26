<?php

namespace Althingi\Injector;

use Althingi\Service\Plenary;

interface ServicePlenaryAwareInterface
{
    public function setPlenaryService(Plenary $plenary): static;
}
