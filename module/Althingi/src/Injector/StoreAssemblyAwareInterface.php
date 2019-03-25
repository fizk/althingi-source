<?php

namespace Althingi\Injector;

use \Althingi\Store\Assembly;

interface StoreAssemblyAwareInterface
{
    /**
     * @param \Althingi\Store\Assembly $assembly
     */
    public function setAssemblyStore(Assembly $assembly);
}
