<?php

namespace Althingi\Injector;

use Althingi\Service\Category;

interface ServiceCategoryAwareInterface
{
    /**
     * @param \Althingi\Service\Category $category
     */
    public function setCategoryService(Category $category);
}
