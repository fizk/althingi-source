<?php

namespace Althingi\Lib;

use Althingi\Service\Speech;

interface ServiceSpeechAwareInterface
{
    /**
     * @param \Althingi\Service\Speech $speech
     */
    public function setSpeechService(Speech $speech);
}
