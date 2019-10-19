<?php

namespace Althingi\Injector;

use Althingi\Store\Document;

interface StoreDocumentAwareInterface
{
    /**
     * @param \Althingi\Store\Document $document
     * @return $this
     */
    public function setDocumentStore(Document $document);
}
