<?php

namespace Althingi\Injector;

use Althingi\Service\SearchIssue;

interface ServiceSearchIssueAwareInterface
{
    /**
     * @param \Althingi\Service\SearchIssue $issue
     */
    public function setSearchIssueService(SearchIssue $issue);
}
