<?php

namespace Althingi\Injector;

use Althingi\Service\CongressmanDocument;

interface ServiceProponentAwareInterface
{
    /**
     * @param CongressmanDocument $congressmanDocument
     */
    public function setCongressmanDocumentService(CongressmanDocument $congressmanDocument);
}
