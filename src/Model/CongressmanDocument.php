<?php

namespace Althingi\Model;

class CongressmanDocument implements ModelInterface
{
    private int $document_id;
    private int $issue_id;
    private string $category;
    private int $assembly_id;
    private int $congressman_id;
    private ?string $minister = null;
    private int $order;

    public function getDocumentId(): int
    {
        return $this->document_id;
    }

    public function setDocumentId(int $document_id): self
    {
        $this->document_id = $document_id;
        return $this;
    }

    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    public function setIssueId(int $issue_id): self
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category)
    {
        $this->category = $category;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): self
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    public function setCongressmanId(int $congressman_id): self
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    public function getMinister(): ?string
    {
        return $this->minister;
    }

    public function setMinister(?string $minister): self
    {
        $this->minister = $minister;
        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

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
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
