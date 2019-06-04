<?php

namespace Althingi\Injector;

use \Althingi\Store\Congressman;

interface StoreCongressmanAwareInterface
{
    /**
     * @param \Althingi\Store\Congressman $congressman
     */
    public function setCongressmanStore(Congressman $congressman);
}
