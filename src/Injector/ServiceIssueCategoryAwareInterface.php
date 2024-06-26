<?php

namespace Althingi\Injector;

use Althingi\Service\IssueCategory;

interface ServiceIssueCategoryAwareInterface
{
    public function setIssueCategoryService(IssueCategory $issueCategory): static;
}
