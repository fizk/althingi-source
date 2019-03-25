<?php

namespace Althingi\Injector;

use Althingi\Service\Committee;

interface ServiceCommitteeAwareInterface
{
    /**
     * @param Committee $committee
     */
    public function setCommitteeService(Committee $committee);
}
