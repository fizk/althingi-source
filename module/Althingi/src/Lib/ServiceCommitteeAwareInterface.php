<?php

namespace Althingi\Lib;

use Althingi\Service\Committee;

interface ServiceCommitteeAwareInterface
{
    /**
     * @param Committee $committee
     */
    public function setCommitteeService(Committee $committee);
}
