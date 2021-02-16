<?php

namespace Althingi\Injector;

use Althingi\Service\Election;

interface ServiceElectionAwareInterface
{
    public function setElectionService(Election $election): self;
}
