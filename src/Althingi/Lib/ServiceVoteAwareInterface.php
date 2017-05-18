<?php

namespace Althingi\Lib;

use Althingi\Service\Vote;

interface ServiceVoteAwareInterface
{
    /**
     * @param Vote $vote
     */
    public function setVoteService(Vote $vote);
}
