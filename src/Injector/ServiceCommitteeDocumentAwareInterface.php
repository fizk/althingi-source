<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeDocument;

interface ServiceCommitteeDocumentAwareInterface
{
    public function setCommitteeDocumentService(CommitteeDocument $committeeDocument): static;
}
