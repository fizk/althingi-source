<?php

namespace Althingi\Injector;

use Althingi\Service\Committee;

interface ServiceCommitteeAwareInterface
{
    public function setCommitteeService(Committee $committee): self;
}
