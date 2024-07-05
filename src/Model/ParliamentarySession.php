<?php

namespace Althingi\Model;

use DateTime;

class ParliamentarySession implements ModelInterface
{
    private $parliamentary_session_id;
    private $assembly_id;
    private ?DateTime $from = null;
    private ?DateTime $to = null;
    private ?string $name = null;

    public function getParliamentarySessionId(): int
    {
        return $this->parliamentary_session_id;
    }

    public function setParliamentarySessionId(int $parliamentary_session_id): static
    {
        $this->parliamentary_session_id = $parliamentary_session_id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'parliamentary_session_id' => $this->parliamentary_session_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from?->format('Y-m-d H:i'),
            'to' => $this->to?->format('Y-m-d H:i'),
            'name' => $this->name,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
