<?php

namespace Althingi\Injector;

use Althingi\Service\Document;

interface ServiceDocumentAwareInterface
{
    public function setDocumentService(Document $document);
}
