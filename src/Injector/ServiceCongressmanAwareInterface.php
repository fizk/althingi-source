<?php

namespace Althingi\Injector;

use Althingi\Service\Congressman;

interface ServiceCongressmanAwareInterface
{
    public function setCongressmanService(Congressman $congressman): static;
}
