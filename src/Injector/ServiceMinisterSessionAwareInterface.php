<?php

namespace Althingi\Injector;

use Althingi\Service\MinisterSession;

interface ServiceMinisterSessionAwareInterface
{
    public function setMinisterSessionService(MinisterSession $ministerSession): static;
}
