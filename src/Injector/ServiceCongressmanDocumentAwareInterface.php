<?php

namespace Althingi\Injector;

use Althingi\Service\CongressmanDocument;

interface ServiceCongressmanDocumentAwareInterface
{
    public function setCongressmanDocumentService(CongressmanDocument $congressmanDocument): static;
}
