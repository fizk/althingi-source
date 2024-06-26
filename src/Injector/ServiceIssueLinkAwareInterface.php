<?php

namespace Althingi\Injector;

use Althingi\Service\IssueLink;

interface ServiceIssueLinkAwareInterface
{
    public function setIssueLinkService(IssueLink $issueLink): static;
}
