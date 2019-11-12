<?php

namespace Althingi\Model;

use DateTime;

class MinisterSitting implements ModelInterface
{
    /** @var int | null */
    private $minister_sitting_id;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $ministry_id;

    /** @var int */
    private $congressman_id;

    /** @var int | null */
    private $party_id = null;

    /** @var \DateTime | null */
    private $from = null;

    /** @var \DateTime | null */
    private $to = null;

    /**
     * @return int
     */
    public function getMinisterSittingId(): ?int
    {
        return $this->minister_sitting_id;
    }

    /**
     * @param int $minister_sitting_id
     * @return MinisterSitting
     */
    public function setMinisterSittingId(?int $minister_sitting_id): MinisterSitting
    {
        $this->minister_sitting_id = $minister_sitting_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return MinisterSitting
     */
    public function setAssemblyId(int $assembly_id): MinisterSitting
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinistryId(): int
    {
        return $this->ministry_id;
    }

    /**
     * @param int $ministry_id
     * @return MinisterSitting
     */
    public function setMinistryId(int $ministry_id): MinisterSitting
    {
        $this->ministry_id = $ministry_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    /**
     * @param int $congressman_id
     * @return MinisterSitting
     */
    public function setCongressmanId(int $congressman_id): MinisterSitting
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPartyId(): ?int
    {
        return $this->party_id;
    }

    /**
     * @param int|null $party_id
     * @return MinisterSitting
     */
    public function setPartyId(?int $party_id): MinisterSitting
    {
        $this->party_id = $party_id;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    /**
     * @param DateTime|null $from
     * @return MinisterSitting
     */
    public function setFrom(?DateTime $from): MinisterSitting
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    /**
     * @param DateTime|null $to
     * @return MinisterSitting
     */
    public function setTo(?DateTime $to): MinisterSitting
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'minister_sitting_id' => $this->minister_sitting_id,
            'assembly_id' => $this->assembly_id,
            'ministry_id' => $this->ministry_id,
            'congressman_id' => $this->congressman_id,
            'party_id' => $this->party_id,
            'from' => $this->from ? $this->from->format('Y-m-d') : null,
            'to' => $this->to ? $this->to->format('Y-m-d') : null,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
