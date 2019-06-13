<?php

namespace Althingi\Injector;

use \Althingi\Store\Vote;

interface StoreVoteAwareInterface
{
    /**
     * @param \Althingi\Store\Vote $vote
     */
    public function setVoteStore(Vote $vote);
}
