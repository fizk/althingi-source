<?php

namespace Althingi\Model;

use DateTime;

class Congressman implements ModelInterface
{
    /** @var  int */
    private $congressman_id;

    /** @var  string */
    private $name;

    /** @var  \DateTime */
    private $birth;

    /** @var  \DateTime */
    private $death;

    /** @var  string */
    private $abbreviation;

    /**
     * @return int
     */
    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    /**
     * @param int $congressman_id
     * @return $this
     */
    public function setCongressmanId(int $congressman_id): Congressman
    {
        $this->congressman_id = $congressman_id;
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
     * @return $this
     */
    public function setName(string $name): Congressman
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirth(): DateTime
    {
        return $this->birth;
    }

    /**
     * @param \DateTime $birth
     * @return $this
     */
    public function setBirth(DateTime $birth = null): Congressman
    {
        $this->birth = $birth;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeath(): ?DateTime
    {
        return $this->death;
    }

    /**
     * @param \DateTime $death
     * @return $this
     */
    public function setDeath(DateTime $death = null): Congressman
    {
        $this->death = $death;
        return $this;
    }

    /**
     * @return string | null
     */
    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    /**
     * @param string $abbreviation | null
     * @return $this
     */
    public function setAbbreviation(?string $abbreviation): Congressman
    {
        $this->abbreviation = $abbreviation;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'congressman_id' => $this->congressman_id,
            'name' => $this->name,
            'birth' => $this->birth ? $this->birth->format('Y-m-d') : null,
            'death' => $this->death ? $this->death->format('Y-m-d') : null,
            'abbreviation' => $this->abbreviation,
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
