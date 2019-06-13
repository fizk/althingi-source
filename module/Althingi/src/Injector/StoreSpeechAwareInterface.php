<?php

namespace Althingi\Injector;

use \Althingi\Store\Speech;

interface StoreSpeechAwareInterface
{
    /**
     * @param \Althingi\Store\Speech $speech
     */
    public function setSpeechStore(Speech $speech);
}
