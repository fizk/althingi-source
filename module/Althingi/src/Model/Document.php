<?php

namespace Althingi\Model;

use DateTime;

class Document implements ModelInterface
{
    /** @var  int */
    private $document_id;

    /** @var  int */
    private $issue_id;

    /** @var  int */
    private $assembly_id;

    /** @var  \DateTime */
    private $date;

    /** @var  string */
    private $url;

    /** @var  string */
    private $type;

    /** @var  string */
    private $category;

    /**
     * @return int
     */
    public function getDocumentId(): int
    {
        return $this->document_id;
    }

    /**
     * @param int $document_id
     * @return Document
     */
    public function setDocumentId(int $document_id): Document
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
     * @return Document
     */
    public function setIssueId(int $issue_id): Document
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
     * @return Document
     */
    public function setAssemblyId(int $assembly_id): Document
    {
        $this->assembly_id = $assembly_id;
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
     * @return Document
     */
    public function setDate(DateTime $date): Document
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Document
     */
    public function setUrl(string $url = null): Document
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Document
     */
    public function setType(string $type): Document
    {
        $this->type = $type;
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
     * @return Document
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
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
            'category' => $this->category,
            'assembly_id' => $this->assembly_id,
            'date' => $this->date ? $this->date->format('Y-m-d H:i:s') : null,
            'url' => $this->url,
            'type' => $this->type,
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
