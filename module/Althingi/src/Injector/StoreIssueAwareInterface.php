<?php

namespace Althingi\Injector;

use \Althingi\Store\Issue;

interface StoreIssueAwareInterface
{
    /**
     * @param \Althingi\Store\Issue $issue
     */
    public function setIssueStore(Issue $issue);
}
