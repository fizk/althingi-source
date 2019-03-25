<?php

namespace Althingi\Injector;

use Althingi\Service\Constituency;

interface ServiceConstituencyAwareInterface
{
    /**
     * @param Constituency $constituency
     */
    public function setConstituencyService(Constituency $constituency);
}
