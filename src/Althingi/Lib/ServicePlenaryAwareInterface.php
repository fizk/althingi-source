<?php

namespace Althingi\Lib;

use Althingi\Service\Plenary;

interface ServicePlenaryAwareInterface
{
    /**
     * @param Plenary $plenary
     */
    public function setPlenaryService(Plenary $plenary);
}
