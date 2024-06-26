<?php

namespace Althingi\Injector;

use Althingi\Service\SuperCategory;

interface ServiceSuperCategoryAwareInterface
{
    public function setSuperCategoryService(SuperCategory $superCategory): static;
}
