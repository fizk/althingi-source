<?php

namespace Althingi\Model;

use DateTime;

class MinisterSession implements ModelInterface
{
    private ?int $minister_session_id = null;
    private int $assembly_id;
    private int $ministry_id;
    private int $congressman_id;
    private ?int $party_id = null;
    private $from = null;
    private ?DateTime $to = null;

    public function getMinisterSessionId(): ?int
    {
        return $this->minister_session_id;
    }

    public function setMinisterSessionId(?int $minister_session_id): static
    {
        $this->minister_session_id = $minister_session_id;
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

    public function getMinistryId(): int
    {
        return $this->ministry_id;
    }

    public function setMinistryId(int $ministry_id): static
    {
        $this->ministry_id = $ministry_id;
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

    public function getPartyId(): ?int
    {
        return $this->party_id;
    }

    public function setPartyId(?int $party_id): static
    {
        $this->party_id = $party_id;
        return $this;
    }

    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    public function setFrom(?DateTime $from): static
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

    public function toArray(): array
    {
        return [
            'minister_session_id' => $this->minister_session_id,
            'assembly_id' => $this->assembly_id,
            'ministry_id' => $this->ministry_id,
            'congressman_id' => $this->congressman_id,
            'party_id' => $this->party_id,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
