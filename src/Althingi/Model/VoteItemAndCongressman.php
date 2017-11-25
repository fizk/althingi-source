<?php

namespace Althingi\Model;

class VoteItemAndCongressman implements ModelInterface
{
    /** @var  \Althingi\Model\VoteItem */
    private $voteItem;

    /** @var  \Althingi\Model\CongressmanPartyProperties */
    private $congressman;

    /**
     * @return VoteItem
     */
    public function getVoteItem(): VoteItem
    {
        return $this->voteItem;
    }

    /**
     * @param VoteItem $voteItem
     * @return VoteItemAndCongressman
     */
    public function setVoteItem(VoteItem $voteItem): VoteItemAndCongressman
    {
        $this->voteItem = $voteItem;
        return $this;
    }

    /**
     * @return CongressmanPartyProperties
     */
    public function getCongressman(): CongressmanPartyProperties
    {
        return $this->congressman;
    }

    /**
     * @param CongressmanPartyProperties $congressman
     * @return VoteItemAndCongressman
     */
    public function setCongressman(CongressmanPartyProperties $congressman): VoteItemAndCongressman
    {
        $this->congressman = $congressman;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->voteItem->toArray(),
            ['congressman' => $this->congressman->toArray()]
        );
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
