<?php

namespace Althingi\Lib;

use Althingi\Service\Issue;

interface ServiceIssueAwareInterface
{
    /**
     * @param Issue $issue
     */
    public function setIssueService(Issue $issue);
}
