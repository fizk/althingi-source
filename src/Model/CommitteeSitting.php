<?php

namespace Althingi\Model;

use DateTime;

class CommitteeSitting implements ModelInterface
{
    /** @var int */
    private $committee_sitting_id;

    /** @var int */
    private $congressman_id;

    /** @var int */
    private $committee_id;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $order;

    /** @var string */
    private $role;

    /** @var \DateTime */
    private $from;

    /** @var \DateTime | null */
    private $to = null;

    /**
     * @return int
     */
    public function getCommitteeSittingId(): ?int
    {
        return $this->committee_sitting_id;
    }

    /**
     * @param int $committee_sitting_id
     * @return CommitteeSitting
     */
    public function setCommitteeSittingId(?int $committee_sitting_id): CommitteeSitting
    {
        $this->committee_sitting_id = $committee_sitting_id;
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
     * @return CommitteeSitting
     */
    public function setCongressmanId(int $congressman_id): CommitteeSitting
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCommitteeId(): int
    {
        return $this->committee_id;
    }

    /**
     * @param int $committee_id
     * @return CommitteeSitting
     */
    public function setCommitteeId(int $committee_id): CommitteeSitting
    {
        $this->committee_id = $committee_id;
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
     * @return CommitteeSitting
     */
    public function setAssemblyId(int $assembly_id): CommitteeSitting
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return CommitteeSitting
     */
    public function setOrder(int $order): CommitteeSitting
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return CommitteeSitting
     */
    public function setRole(string $role): CommitteeSitting
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): DateTime
    {
        return $this->from;
    }

    /**
     * @param \DateTime $from
     * @return CommitteeSitting
     */
    public function setFrom(DateTime $from): CommitteeSitting
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    /**
     * @param \DateTime|null $to
     * @return CommitteeSitting
     */
    public function setTo(?DateTime $to): CommitteeSitting
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'committee_sitting_id' => $this->committee_sitting_id,
            'congressman_id' => $this->congressman_id,
            'committee_id' => $this->committee_id,
            'assembly_id' => $this->assembly_id,
            'order' => $this->order,
            'role' => $this->role,
            'from' => $this->from ? $this->from->format('Y-m-d') : null,
            'to' => $this->to ? $this->to->format('Y-m-d') : null,

        ];
    }
}
