<?php

namespace Althingi\Injector;

interface StallingAwareInterface
{
    /**
     * @param int $time
     * @return $this
     */
    public function setStallTime(int $time);
}
