<?php

namespace Althingi\Injector;

use Althingi\Service\Constituency;

interface ServiceConstituencyAwareInterface
{
    public function setConstituencyService(Constituency $constituency): self;
}
