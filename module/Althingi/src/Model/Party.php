<?php

namespace Althingi\Model;

class Party implements ModelInterface
{
    /** @var  int */
    private $party_id;

    /** @var  string */
    private $name;

    /** @var  string */
    private $abbr_short;

    /** @var  string */
    private $abbr_long;

    /** @var  string */
    private $color;

    /**
     * @return int
     */
    public function getPartyId(): int
    {
        return $this->party_id;
    }

    /**
     * @param int $party_id
     * @return Party
     */
    public function setPartyId(int $party_id): Party
    {
        $this->party_id = $party_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Party
     */
    public function setName(string $name): Party
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbbrShort(): string
    {
        return $this->abbr_short;
    }

    /**
     * @param string $abbr_short
     * @return Party
     */
    public function setAbbrShort(string $abbr_short = null): Party
    {
        $this->abbr_short = $abbr_short;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbbrLong(): string
    {
        return $this->abbr_long;
    }

    /**
     * @param string $abbr_long
     * @return Party
     */
    public function setAbbrLong(string $abbr_long = null): Party
    {
        $this->abbr_long = $abbr_long;
        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return Party
     */
    public function setColor(string $color = null): Party
    {
        $this->color = $color;
        return $this;
    }

    public function toArray()
    {
        return [
            'party_id' => $this->party_id,
            'name' => $this->name,
            'abbr_short' => $this->abbr_short,
            'abbr_long' => $this->abbr_long,
            'color' => $this->color,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
