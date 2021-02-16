<?php

namespace Althingi\Injector;

use Althingi\Service\Session;

interface ServiceSessionAwareInterface
{
    public function setSessionService(Session $session): self;
}
