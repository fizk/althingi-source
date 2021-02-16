<?php

namespace Althingi\Injector;

use Althingi\Service\VoteItem;

interface ServiceVoteItemAwareInterface
{
    public function setVoteItemService(VoteItem $voteItem): self;
}
