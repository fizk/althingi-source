<?php

namespace Althingi\Model;

use DateTime;

class Election implements ModelInterface
{
    /** @var  int */
    private $election_id;

    /** @var  \DateTime */
    private $date;

    /** @var  string */
    private $title;

    /** @var  string */
    private $description;

    /**
     * @return int
     */
    public function getElectionId(): int
    {
        return $this->election_id;
    }

    /**
     * @param int $election_id
     * @return Election
     */
    public function setElectionId(int $election_id): Election
    {
        $this->election_id = $election_id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Election
     */
    public function setDate(DateTime $date): Election
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Election
     */
    public function setTitle(string $title = null): Election
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Election
     */
    public function setDescription(string $description = null): Election
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'election_id' => $this->election_id,
            'date' => $this->date->format('Y-m-d'),
            'title' => $this->title,
            'description' => $this->description,
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
