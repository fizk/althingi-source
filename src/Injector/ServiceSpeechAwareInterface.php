<?php

namespace Althingi\Injector;

use Althingi\Service\Speech;

interface ServiceSpeechAwareInterface
{
    public function setSpeechService(Speech $speech): static;
}
