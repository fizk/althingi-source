<?php

namespace Althingi\Lib;

use Althingi\Service\Document;

interface ServiceDocumentAwareInterface
{
    /**
     * @param Document $document
     */
    public function setDocumentService(Document $document);
}
