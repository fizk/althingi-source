<?php

namespace Althingi\Lib;

use \Althingi\Store\Assembly;

interface StoreAssemblyAwareInterface
{
    /**
     * @param \Althingi\Store\Assembly $assembly
     */
    public function setAssemblyStore(Assembly $assembly);
}
