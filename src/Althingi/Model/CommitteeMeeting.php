<?php

namespace Althingi\Model;

use DateTime;

class CommitteeMeeting implements ModelInterface
{
    /** @var  int */
    private $committee_meeting_id;

    /** @var  int */
    private $committee_id;

    /** @var  int */
    private $assembly_id;

    /** @var  DateTime */
    private $from;

    /** @var  DateTime */
    private $to;

    /** @var  string */
    private $description;

    /**
     * @return int
     */
    public function getCommitteeMeetingId(): int
    {
        return $this->committee_meeting_id;
    }

    /**
     * @param int $committee_meeting_id
     * @return CommitteeMeeting
     */
    public function setCommitteeMeetingId(int $committee_meeting_id): CommitteeMeeting
    {
        $this->committee_meeting_id = $committee_meeting_id;
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
     * @return CommitteeMeeting
     */
    public function setCommitteeId(int $committee_id): CommitteeMeeting
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
     * @return CommitteeMeeting
     */
    public function setAssemblyId(int $assembly_id): CommitteeMeeting
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    /**
     * @param DateTime $from
     * @return CommitteeMeeting
     */
    public function setFrom(DateTime $from = null): CommitteeMeeting
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    /**
     * @param DateTime $to
     * @return CommitteeMeeting
     */
    public function setTo(DateTime $to = null): CommitteeMeeting
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return CommitteeMeeting
     */
    public function setDescription(string $description = null): CommitteeMeeting
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'committee_meeting_id' => $this->committee_meeting_id,
            'committee_id' => $this->committee_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from ? $this->from->format('Y-m-d H:i') : null,
            'to' => $this->to ? $this->to->format('Y-m-d H:i') : null,
            'description' => $this->description,
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
