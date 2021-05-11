<?php

namespace Althingi\Model;

class CongressmanDocument implements ModelInterface
{
    /** @var int */
    private $document_id;

    /** @var int */
    private $issue_id;

    /** @var  string */
    private $category;

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
    public function setDocumentId(int $document_id): self
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
    public function setIssueId(int $issue_id): self
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return CongressmanDocument
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
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
    public function setAssemblyId(int $assembly_id): self
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
    public function setCongressmanId(int $congressman_id): self
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
    public function setMinister(string $minister = null): self
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
    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'document_id' => $this->document_id,
            'issue_id' => $this->issue_id,
            'category' => $this->category,
            'assembly_id' => $this->assembly_id,
            'congressman_id' => $this->congressman_id,
            'minister' => $this->minister,
            'order' => $this->order,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
