<?php

namespace Althingi\Lib;

use Althingi\Service\SuperCategory;

interface ServiceSuperCategoryAwareInterface
{
    /**
     * @param SuperCategory $superCategory
     */
    public function setSuperCategoryService(SuperCategory $superCategory);
}
