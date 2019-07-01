<?php

namespace Althingi\Injector;

use Althingi\Service\IssueLink;

interface ServiceIssueLinkAwareInterface
{
    /**
     * @param \Althingi\Service\IssueLink $issueLink
     */
    public function setIssueLinkService(IssueLink $issueLink);
}
