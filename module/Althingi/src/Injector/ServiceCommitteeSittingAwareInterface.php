<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeSitting;

interface ServiceCommitteeSittingAwareInterface
{
    /**
     * @param CommitteeSitting $committeeSitting
     * @return $this;
     */
    public function setCommitteeSitting(CommitteeSitting $committeeSitting);
}
