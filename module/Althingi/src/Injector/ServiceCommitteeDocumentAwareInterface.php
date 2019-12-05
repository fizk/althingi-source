<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeDocument;

interface ServiceCommitteeDocumentAwareInterface
{
    /**
     * @param CommitteeDocument $committeeDocument
     * @return $this;
     */
    public function setCommitteeDocument(CommitteeDocument $committeeDocument);
}
