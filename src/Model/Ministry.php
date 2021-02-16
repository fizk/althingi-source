<?php

namespace Althingi\Model;

class Ministry implements ModelInterface
{
    /** @var int */
    private $ministry_id;

    /** @var string */
    private $name;

    /** @var string | null */
    private $abbr_short = null;

    /** @var string | null */
    private $abbr_long = null;

    /** @var int | null */
    private $first = null;

    /** @var int | null */
    private $last = null;

    /**
     * @return int
     */
    public function getMinistryId(): int
    {
        return $this->ministry_id;
    }

    /**
     * @param int $ministry_id
     * @return Ministry
     */
    public function setMinistryId(int $ministry_id): Ministry
    {
        $this->ministry_id = $ministry_id;
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
     * @return Ministry
     */
    public function setName(string $name): Ministry
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAbbrShort(): ?string
    {
        return $this->abbr_short;
    }

    /**
     * @param string|null $abbr_short
     * @return Ministry
     */
    public function setAbbrShort(?string $abbr_short): Ministry
    {
        $this->abbr_short = $abbr_short;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAbbrLong(): ?string
    {
        return $this->abbr_long;
    }

    /**
     * @param string|null $abbr_long
     * @return Ministry
     */
    public function setAbbrLong(?string $abbr_long): Ministry
    {
        $this->abbr_long = $abbr_long;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFirst(): ?int
    {
        return $this->first;
    }

    /**
     * @param int|null $first
     * @return Ministry
     */
    public function setFirst(?int $first): Ministry
    {
        $this->first = $first;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLast(): ?int
    {
        return $this->last;
    }

    /**
     * @param int|null $last
     * @return Ministry
     */
    public function setLast(?int $last): Ministry
    {
        $this->last = $last;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'ministry_id' => $this->ministry_id,
            'name' => $this->name,
            'abbr_short' => $this->abbr_short,
            'abbr_long' => $this->abbr_long,
            'first' => $this->first,
            'last' => $this->last,
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
