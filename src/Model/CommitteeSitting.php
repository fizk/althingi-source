<?php

namespace Althingi\Model;

use DateTime;

class CommitteeSitting implements ModelInterface
{
    private $committee_sitting_id;
    private $congressman_id;
    private $committee_id;
    private $assembly_id;
    private ?int $order = null;
    private ?string $role = null;
    private DateTime $from;
    private ?DateTime $to = null;

    public function getCommitteeSittingId(): ?int
    {
        return $this->committee_sitting_id;
    }

    public function setCommitteeSittingId(?int $committee_sitting_id): static
    {
        $this->committee_sitting_id = $committee_sitting_id;
        return $this;
    }

    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    public function setCongressmanId(int $congressman_id): static
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    public function getCommitteeId(): int
    {
        return $this->committee_id;
    }

    public function setCommitteeId(int $committee_id): static
    {
        $this->committee_id = $committee_id;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): static
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getFrom(): DateTime
    {
        return $this->from;
    }

    public function setFrom(DateTime $from): static
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    public function setTo(?DateTime $to): static
    {
        $this->to = $to;
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'committee_sitting_id' => $this->committee_sitting_id,
            'congressman_id' => $this->congressman_id,
            'committee_id' => $this->committee_id,
            'assembly_id' => $this->assembly_id,
            'order' => $this->order,
            'role' => $this->role,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),

        ];
    }
}
