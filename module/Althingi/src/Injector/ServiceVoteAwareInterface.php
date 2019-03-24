<?php

namespace Althingi\Injector;

use Althingi\Service\Vote;

interface ServiceVoteAwareInterface
{
    /**
     * @param \Althingi\Service\Vote $vote
     */
    public function setVoteService(Vote $vote);
}
