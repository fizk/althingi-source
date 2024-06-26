<?php

namespace Althingi\Model;

use DateTime;

class Congressman implements ModelInterface
{
    private $congressman_id;
    private $name;
    private $birth;
    private ?DateTime $death = null;
    private ?string $abbreviation = null;

    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    public function setCongressmanId(int $congressman_id): static
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getBirth(): DateTime
    {
        return $this->birth;
    }

    public function setBirth(?DateTime $birth): static
    {
        $this->birth = $birth;
        return $this;
    }

    public function getDeath(): ?DateTime
    {
        return $this->death;
    }

    public function setDeath(?DateTime $death): static
    {
        $this->death = $death;
        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): static
    {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'congressman_id' => $this->congressman_id,
            'name' => $this->name,
            'birth' => $this->birth?->format('Y-m-d'),
            'death' => $this->death?->format('Y-m-d'),
            'abbreviation' => $this->abbreviation,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
