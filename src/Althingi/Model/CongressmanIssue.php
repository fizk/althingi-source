<?php

namespace Althingi\Model;

class CongressmanIssue implements ModelInterface
{
    /** @var  int */
    private $order;

    /** @var  string */
    private $type;

    /** @var  string */
    private $type_name;

    /** @var  string */
    private $type_subname;

    /** @var  string */
    private $document_type;

    /** @var  int */
    private $count;

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return CongressmanIssue
     */
    public function setOrder(int $order): CongressmanIssue
    {
        $this->order = $order;
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
     * @return CongressmanIssue
     */
    public function setType(string $type): CongressmanIssue
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->type_name;
    }

    /**
     * @param string $type_name
     * @return CongressmanIssue
     */
    public function setTypeName(string $type_name): CongressmanIssue
    {
        $this->type_name = $type_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeSubname(): string
    {
        return $this->type_subname;
    }

    /**
     * @param string $type_subname
     * @return CongressmanIssue
     */
    public function setTypeSubname(string $type_subname): CongressmanIssue
    {
        $this->type_subname = $type_subname;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocumentType(): string
    {
        return $this->document_type;
    }

    /**
     * @param string $document_type
     * @return CongressmanIssue
     */
    public function setDocumentType(string $document_type): CongressmanIssue
    {
        $this->document_type = $document_type;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return CongressmanIssue
     */
    public function setCount(int $count): CongressmanIssue
    {
        $this->count = $count;
        return $this;
    }

    public function toArray()
    {
        return [
            'order' => $this->order,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'type_subname' => $this->type_subname,
            'document_type' => $this->document_type,
            'count' => $this->count,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
