<?php

namespace Althingi\Injector;

use Althingi\Service\Category;

interface ServiceCategoryAwareInterface
{
    public function setCategoryService(Category $category): self;
}
