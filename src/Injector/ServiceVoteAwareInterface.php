<?php

namespace Althingi\Injector;

use Althingi\Service\Vote;

interface ServiceVoteAwareInterface
{
    public function setVoteService(Vote $vote): self;
}
