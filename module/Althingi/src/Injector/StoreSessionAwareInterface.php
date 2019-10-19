<?php

namespace Althingi\Injector;

use \Althingi\Store\Session;

interface StoreSessionAwareInterface
{
    /**
     * @param \Althingi\Store\Session $session
     * @return $this
     */
    public function setSessionStore(Session $session);
}
