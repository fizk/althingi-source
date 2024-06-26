<?php

namespace Althingi\Model;

class CongressmanIssue implements ModelInterface
{
    private ?int $order = null;
    private ?string $type = null;
    private ?string $type_name = null;
    private ?string $type_subname = null;
    private ?string $document_type = null;
    private ?int $count = null;

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function setTypeName(?string $type_name): static
    {
        $this->type_name = $type_name;
        return $this;
    }

    public function getTypeSubname(): ?string
    {
        return $this->type_subname;
    }

    public function setTypeSubname(?string $type_subname): static
    {
        $this->type_subname = $type_subname;
        return $this;
    }

    public function getDocumentType(): ?string
    {
        return $this->document_type;
    }

    public function setDocumentType(?string $document_type): static
    {
        $this->document_type = $document_type;
        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): static
    {
        $this->count = $count;
        return $this;
    }

    public function toArray(): array
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

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
