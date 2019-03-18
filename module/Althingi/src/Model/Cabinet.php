<?php

namespace Althingi\Model;

class Cabinet implements ModelInterface
{
    /** @var  int */
    private $cabinet_id;

    /** @var  string */
    private $title;

    /** @var  \DateTime */
    private $from;

    /** @var  \DateTime */
    private $to;

    /** @var  string */
    private $description;

    /**
     * @return int
     */
    public function getCabinetId(): int
    {
        return $this->cabinet_id;
    }

    /**
     * @param int $cabinet_id
     * @return Cabinet
     */
    public function setCabinetId(int $cabinet_id): Cabinet
    {
        $this->cabinet_id = $cabinet_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Cabinet
     */
    public function setTitle(string $title): Cabinet
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): ?\DateTime
    {
        return $this->from;
    }

    /**
     * @param \DateTime $from
     * @return Cabinet
     */
    public function setFrom(?\DateTime $from): Cabinet
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTo(): ?\DateTime
    {
        return $this->to;
    }

    /**
     * @param \DateTime $to
     * @return Cabinet
     */
    public function setTo(?\DateTime $to): Cabinet
    {
        $this->to = $to;
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
     * @return Cabinet
     */
    public function setDescription(?string $description): Cabinet
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
            'cabinet_id' => $this->cabinet_id,
            'title' => $this->title,
            'description' => $this->description,
            'from' => $this->from ? $this->from->format('Y-m-d') : null,
            'to' => $this->to ? $this->to->format('Y-m-d') : null,
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
