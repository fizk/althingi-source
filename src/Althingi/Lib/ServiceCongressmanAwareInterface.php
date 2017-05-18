<?php

namespace Althingi\Lib;

use Althingi\Service\Congressman;

interface ServiceCongressmanAwareInterface
{
    /**
     * @param Congressman $congressman
     */
    public function setCongressmanService(Congressman $congressman);
}
