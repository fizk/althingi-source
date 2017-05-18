<?php

namespace Althingi\Lib;

use Althingi\Service\VoteItem;

interface ServiceVoteItemAwareInterface
{
    /**
     * @param VoteItem $voteItem
     */
    public function setVoteItemService(VoteItem $voteItem);
}
