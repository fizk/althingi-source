<?php

namespace Althingi\Injector;

use Althingi\Service\Issue;

interface ServiceIssueAwareInterface
{
    public function setIssueService(Issue $issue): self;
}
