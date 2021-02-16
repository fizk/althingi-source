<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeDocument;

interface ServiceCommitteeDocumentAwareInterface
{
    public function setCommitteeDocument(CommitteeDocument $committeeDocument): self;
}
