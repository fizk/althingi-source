<?php

namespace Althingi\Model;

use DateTime;

class Session implements ModelInterface
{
    private ?int $session_id = null;
    private int $congressman_id;
    private int $constituency_id;
    private int $assembly_id;
    private ?int $party_id = null;
    private ?DateTime $from = null;
    private ?DateTime $to = null;
    private ?string $type = null;
    private ?string $abbr = null;

    public function getSessionId(): ?int
    {
        return $this->session_id;
    }

    public function setSessionId(?int $session_id): static
    {
        $this->session_id = $session_id;
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

    public function getConstituencyId(): int
    {
        return $this->constituency_id;
    }

    public function setConstituencyId(int $constituency_id): static
    {
        $this->constituency_id = $constituency_id;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getAbbr(): ?string
    {
        return $this->abbr;
    }

    public function setAbbr(?string $abbr): static
    {
        $this->abbr = $abbr;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'session_id' => $this->session_id,
            'congressman_id' => $this->congressman_id,
            'constituency_id' => $this->constituency_id,
            'assembly_id' => $this->assembly_id,
            'party_id' => $this->party_id,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
            'type' => $this->type,
            'abbr' => $this->abbr,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
