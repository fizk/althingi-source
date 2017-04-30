<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class CongressmanAndParty extends Congressman
{

    /** @var  int */
    private $party_id;

    /**
     * @return int
     */
    public function getPartyId(): ?int
    {
        return $this->party_id;
    }

    /**
     * @param int $party_id
     * @return CongressmanAndParty
     */
    public function setPartyId(int $party_id = null): CongressmanAndParty
    {
        $this->party_id = $party_id;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            parent::toArray(),
            ['party_id' => $this->party_id]
        );
    }
}
