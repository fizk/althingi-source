<?php

namespace Althingi\Model;

use DateTime;

class PresidentCongressman extends Congressman
{
    /** @var int */
    private $president_id;

    /** @var int */
    private $assembly_id;

    /** @var \DateTime */
    private $from;

    /** @var \DateTime */
    private $to;

    /** @var string */
    private $title;

    /** @var string */
    private $abbr;

    /**
     * @return int
     */
    public function getPresidentId(): int
    {
        return $this->president_id;
    }

    /**
     * @param int $president_id
     * @return PresidentCongressman
     */
    public function setPresidentId(int $president_id): PresidentCongressman
    {
        $this->president_id = $president_id;
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
     * @return PresidentCongressman
     */
    public function setAssemblyId(int $assembly_id): PresidentCongressman
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): DateTime
    {
        return $this->from;
    }

    /**
     * @param \DateTime $from
     * @return PresidentCongressman
     */
    public function setFrom(DateTime $from): PresidentCongressman
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    /**
     * @param \DateTime $to
     * @return PresidentCongressman
     */
    public function setTo(DateTime $to = null): PresidentCongressman
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
     * @return PresidentCongressman
     */
    public function setTitle(string $title): PresidentCongressman
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
     * @return PresidentCongressman
     */
    public function setAbbr(string $abbr = null): PresidentCongressman
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
        return array_merge(parent::toArray(), [
            'president_id' => $this->president_id,
            'assembly_id' => $this->assembly_id,
            'from' => $this->from->format('Y-m-d'),
            'to' => $this->to ? $this->to->format('Y-m-d') : null,
            'title' => $this->title,
            'abbr' => $this->abbr,
        ]);
    }
}
