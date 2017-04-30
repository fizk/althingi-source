<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class CongressmanDocument implements ModelInterface
{
    /** @var int */
    private $document_id;

    /** @var int */
    private $issue_id;

    /** @var int */
    private $assembly_id;

    /** @var int */
    private $congressman_id;

    /** @var string */
    private $minister;

    /** @var int */
    private $order;

    /**
     * @return int
     */
    public function getDocumentId(): int
    {
        return $this->document_id;
    }

    /**
     * @param int $document_id
     * @return CongressmanDocument
     */
    public function setDocumentId(int $document_id): CongressmanDocument
    {
        $this->document_id = $document_id;
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
     * @return CongressmanDocument
     */
    public function setIssueId(int $issue_id): CongressmanDocument
    {
        $this->issue_id = $issue_id;
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
     * @return CongressmanDocument
     */
    public function setAssemblyId(int $assembly_id): CongressmanDocument
    {
        $this->assembly_id = $assembly_id;
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
     * @return CongressmanDocument
     */
    public function setCongressmanId(int $congressman_id): CongressmanDocument
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMinister(): ?string
    {
        return $this->minister;
    }

    /**
     * @param string|null $minister
     * @return CongressmanDocument
     */
    public function setMinister(string $minister = null): CongressmanDocument
    {
        $this->minister = $minister;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return CongressmanDocument
     */
    public function setOrder(int $order): CongressmanDocument
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'document_id' => $this->document_id,
            'issue_id' => $this->issue_id,
            'assembly_id' => $this->assembly_id,
            'congressman_id' => $this->congressman_id,
            'minister' => $this->minister,
            'order' => $this->order,
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
