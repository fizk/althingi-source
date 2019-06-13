<?php

namespace Althingi\Injector;

use \Althingi\Store\Category;

interface StoreCategoryAwareInterface
{
    /**
     * @param \Althingi\Store\Category $category
     */
    public function setCategoryStore(Category $category);
}
