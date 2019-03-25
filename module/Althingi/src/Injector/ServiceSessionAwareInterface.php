<?php

namespace Althingi\Injector;

use Althingi\Service\Session;

interface ServiceSessionAwareInterface
{
    /**
     * @param Session $session
     */
    public function setSessionService(Session $session);
}
