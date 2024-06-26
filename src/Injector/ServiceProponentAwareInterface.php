<?php

namespace Althingi\Injector;

use Althingi\Service\CongressmanDocument;

interface ServiceProponentAwareInterface
{
    public function setCongressmanDocumentService(CongressmanDocument $congressmanDocument): static;
}
