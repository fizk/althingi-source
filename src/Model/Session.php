<?php

namespace Althingi\Model;

use DateTime;

class Session implements ModelInterface
{
    /** @var int */
    private $session_id;

    /** @var int */
    private $congressman_id;

    /** @var int */
    private $constituency_id;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $party_id;

    /** @var \DateTime */
    private $from;

    /** @var \DateTime */
    private $to;

    /** @var string */
    private $type;

    /** @var string */
    private $abbr;

    /**
     * @return int
     */
    public function getSessionId(): ?int
    {
        return $this->session_id;
    }

    /**
     * @param int $session_id
     * @return Session
     */
    public function setSessionId(int $session_id = null): Session
    {
        $this->session_id = $session_id;
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
     * @return Session
     */
    public function setCongressmanId(int $congressman_id): Session
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getConstituencyId(): int
    {
        return $this->constituency_id;
    }

    /**
     * @param int $constituency_id
     * @return Session
     */
    public function setConstituencyId(int $constituency_id): Session
    {
        $this->constituency_id = $constituency_id;
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
     * @return Session
     */
    public function setAssemblyId(int $assembly_id): Session
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPartyId(): ?int
    {
        return $this->party_id;
    }

    /**
     * @param int $party_id
     * @return Session
     */
    public function setPartyId(int $party_id = null): Session
    {
        $this->party_id = $party_id;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    /**
     * @param \DateTime|null $from
     * @return Session
     */
    public function setFrom(DateTime $from = null): Session
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    /**
     * @param \DateTime|null $to
     * @return Session
     */
    public function setTo(DateTime $to = null): Session
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     * @return Session
     */
    public function setType(string $type = null): Session
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAbbr(): ?string
    {
        return $this->abbr;
    }

    /**
     * @param string|null $abbr
     * @return Session
     */
    public function setAbbr(string $abbr = null): Session
    {
        $this->abbr = $abbr;
        return $this;
    }

    public function toArray()
    {
        return [
            'session_id' => $this->session_id,
            'congressman_id' => $this->congressman_id,
            'constituency_id' => $this->constituency_id,
            'assembly_id' => $this->assembly_id,
            'party_id' => $this->party_id,
            'from' => $this->from ? $this->from->format('Y-m-d') : null,
            'to' => $this->to ? $this->to->format('Y-m-d') : null,
            'type' => $this->type,
            'abbr' => $this->abbr,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
