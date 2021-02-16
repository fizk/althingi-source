<?php

namespace Althingi\Injector;

use Althingi\Service\SearchAssembly;

interface ServiceSearchAssemblyAwareInterface
{
    /**
     * @param \Althingi\Service\SearchAssembly $assembly
     */
    public function setSearchAssemblyService(SearchAssembly $assembly);
}
