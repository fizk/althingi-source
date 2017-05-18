<?php

namespace Althingi\Lib;

use Althingi\Service\Assembly;

interface ServiceAssemblyAwareInterface
{
    /**
     * @param Assembly $assembly
     */
    public function setAssemblyService(Assembly $assembly);
}
