<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class Speech implements ModelInterface
{

    /** @var string */
    private $speech_id;

    /** @var int */
    private $plenary_id;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $issue_id;

    /** @var int */
    private $congressman_id;

    /** @var string */
    private $congressman_type = null;

    /** @var \DateTime */
    private $from = null;

    /** @var \DateTime */
    private $to = null;

    /** @var string */
    private $text = null;

    /** @var string */
    private $type = null;

    /** @var string */
    private $iteration = null;

    /**
     * @return string
     */
    public function getSpeechId(): string
    {
        return $this->speech_id;
    }

    /**
     * @param string $speech_id
     * @return Speech
     */
    public function setSpeechId(string $speech_id): Speech
    {
        $this->speech_id = $speech_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlenaryId(): int
    {
        return $this->plenary_id;
    }

    /**
     * @param int $plenary_id
     * @return Speech
     */
    public function setPlenaryId(int $plenary_id): Speech
    {
        $this->plenary_id = $plenary_id;
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
     * @return Speech
     */
    public function setAssemblyId(int $assembly_id): Speech
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    /**
     * @param int $issue_id
     * @return Speech
     */
    public function setIssueId(int $issue_id): Speech
    {
        $this->issue_id = $issue_id;
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
     * @return Speech
     */
    public function setCongressmanId(int $congressman_id): Speech
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCongressmanType(): ?string
    {
        return $this->congressman_type;
    }

    /**
     * @param string $congressman_type
     * @return Speech
     */
    public function setCongressmanType(string $congressman_type = null): Speech
    {
        $this->congressman_type = $congressman_type;
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
     * @return Speech
     */
    public function setFrom(\DateTime $from = null): Speech
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
     * @return Speech
     */
    public function setTo(\DateTime $to = null): Speech
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return Speech
     */
    public function setText(string $text = null): Speech
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Speech
     */
    public function setType(string $type = null): Speech
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getIteration(): ?string
    {
        return $this->iteration;
    }

    /**
     * @param string $iteration
     * @return Speech
     */
    public function setIteration(string $iteration = null): Speech
    {
        $this->iteration = $iteration;
        return $this;
    }

    public function toArray()
    {
        return [
            'speech_id' => $this->speech_id,
            'plenary_id' => $this->plenary_id,
            'assembly_id' => $this->assembly_id,
            'issue_id' => $this->issue_id,
            'congressman_id' => $this->congressman_id,
            'congressman_type' => $this->congressman_type,
            'from' => $this->from ? $this->from->format('Y-m-d H:i:s') : null,
            'to' => $this->to ? $this->to->format('Y-m-d H:i:s') : null,
            'text' => $this->text,
            'type' => $this->type,
            'iteration' => $this->iteration,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
