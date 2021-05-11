<?php

namespace Althingi\Model;

use DateTime;

class CommitteeMeeting implements ModelInterface
{
    private int $committee_meeting_id;
    private int $committee_id;
    private int $assembly_id;
    private ?DateTime $from = null;
    private ?DateTime $to = null;
    private ?string $description = null;

    public function getCommitteeMeetingId(): int
    {
        return $this->committee_meeting_id;
    }

    public function setCommitteeMeetingId(int $committee_meeting_id): self
    {
        $this->committee_meeting_id = $committee_meeting_id;
        return $this;
    }

    public function getCommitteeId(): int
    {
        return $this->committee_id;
    }

    public function setCommitteeId(int $committee_id): self
    {
        $this->committee_id = $committee_id;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): self
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    public function setFrom(?DateTime $from = null): self
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    public function setTo(?DateTime $to = null): self
    {
        $this->to = $to;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description = null): self
    {
        $this->description = $description;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'committee_meeting_id' => $this->committee_meeting_id,
            'committee_id' => $this->committee_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from?->format('Y-m-d H:i'),
            'to' => $this->to?->format('Y-m-d H:i'),
            'description' => $this->description,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
