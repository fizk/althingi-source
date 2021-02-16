<?php

namespace Althingi\Injector;

use Althingi\Service\SearchSpeech;

interface ServiceSearchSpeechAwareInterface
{
    /**
     * @param \Althingi\Service\SearchSpeech $speech
     */
    public function setSearchSpeechService(SearchSpeech $speech);
}
