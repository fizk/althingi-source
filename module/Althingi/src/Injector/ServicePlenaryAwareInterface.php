<?php

namespace Althingi\Injector;

use Althingi\Service\Plenary;

interface ServicePlenaryAwareInterface
{
    /**
     * @param Plenary $plenary
     */
    public function setPlenaryService(Plenary $plenary);
}
