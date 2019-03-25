<?php

namespace Althingi\Injector;

use Althingi\Service\VoteItem;

interface ServiceVoteItemAwareInterface
{
    /**
     * @param VoteItem $voteItem
     */
    public function setVoteItemService(VoteItem $voteItem);
}
