<?php

namespace Althingi\Lib;

use Althingi\Service\IssueCategory;

interface ServiceIssueCategoryAwareInterface
{
    /**
     * @param IssueCategory $issueCategory
     */
    public function setIssueCategoryService(IssueCategory $issueCategory);
}
