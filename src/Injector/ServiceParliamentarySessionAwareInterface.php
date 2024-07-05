<?php

namespace Althingi\Injector;

use Althingi\Service\ParliamentarySession;

interface ServiceParliamentarySessionAwareInterface
{
    public function setParliamentarySession(ParliamentarySession $parliamentarySession): static;
}
