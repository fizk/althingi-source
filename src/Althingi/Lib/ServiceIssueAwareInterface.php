<?php

namespace Althingi\Lib;

use Althingi\Service\Issue;

interface ServiceIssueAwareInterface
{
    /**
     * @param \Althingi\Service\Issue $issue
     */
    public function setIssueService(Issue $issue);
}
