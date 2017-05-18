<?php

namespace Althingi\Lib;

use Althingi\Service\Speech;

interface ServiceSpeechAwareInterface
{
    /**
     * @param Speech $speech
     */
    public function setSpeechService(Speech $speech);
}
