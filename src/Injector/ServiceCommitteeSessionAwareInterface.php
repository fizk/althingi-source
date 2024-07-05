<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeSession;

interface ServiceCommitteeSessionAwareInterface
{
    public function setCommitteeSession(CommitteeSession $committeeSession): static;
}
