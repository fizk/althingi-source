<?php

namespace Althingi\Lib;

use Althingi\Service\President;

interface ServicePresidentAwareInterface
{
    /**
     * @param President $president
     */
    public function setPresidentService(President $president);
}
