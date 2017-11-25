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

    /**
     * @return int
     */
    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    /**
     * @param int $congressman_id
     * @return Congressman
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
     * @return Congressman
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
     * @return Congressman
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
     * @return Congressman
     */
    public function setDeath(DateTime $death = null): Congressman
    {
        $this->death = $death;
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
