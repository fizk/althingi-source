<?php

namespace Althingi\Lib;

use Althingi\Service\Category;

interface ServiceCategoryAwareInterface
{
    /**
     * @param Category $category
     */
    public function setCategoryService(Category $category);
}
