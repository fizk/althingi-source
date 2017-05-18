<?php

namespace Althingi\Model;

use DateTime;

class President implements ModelInterface
{
    /** @var int */
    private $president_id;

    /** @var int */
    private $congressman_id;

    /** @var int */
    private $assembly_id;

    /** @var DateTime */
    private $from;

    /** @var DateTime */
    private $to;

    /** @var string */
    private $title;

    /** @var string */
    private $abbr;

    /**
     * @return int
     */
    public function getPresidentId(): ?int
    {
        return $this->president_id;
    }

    /**
     * @param int $president_id
     * @return President
     */
    public function setPresidentId(int $president_id = null): President
    {
        $this->president_id = $president_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    /**
     * @param int $congressman_id
     * @return President
     */
    public function setCongressmanId(int $congressman_id): President
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return President
     */
    public function setAssemblyId(int $assembly_id): President
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFrom(): DateTime
    {
        return $this->from;
    }

    /**
     * @param DateTime $from
     * @return President
     */
    public function setFrom(DateTime $from): President
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    /**
     * @param DateTime $to
     * @return President
     */
    public function setTo(DateTime $to = null): President
    {
        $this->to = $to;
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
     * @return President
     */
    public function setTitle(string $title): President
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbbr(): ?string
    {
        return $this->abbr;
    }

    /**
     * @param string $abbr
     * @return President
     */
    public function setAbbr(string $abbr = null): President
    {
        $this->abbr = $abbr;
        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'president_id' => $this->president_id,
            'congressman_id' => $this->congressman_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from ? $this->from->format('Y-m-d') : null,
            'to' => $this->to ? $this->to->format('Y-m-d') : null,
            'title' => $this->title,
            'abbr' => $this->abbr,
        ];
    }
}
