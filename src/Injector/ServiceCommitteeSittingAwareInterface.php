<?php

namespace Althingi\Injector;

use Althingi\Service\CommitteeSitting;

interface ServiceCommitteeSittingAwareInterface
{
    public function setCommitteeSitting(CommitteeSitting $committeeSitting): static;
}
