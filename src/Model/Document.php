<?php

namespace Althingi\Model;

use DateTime;

class Document implements ModelInterface
{
    private int $document_id;
    private int $issue_id;
    private int $assembly_id;
    private DateTime $date;
    private ?string $url = null;
    private string $type;
    private KindEnum $kind;

    public function getDocumentId(): int
    {
        return $this->document_id;
    }

    public function setDocumentId(int $document_id): static
    {
        $this->document_id = $document_id;
        return $this;
    }

    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    public function setIssueId(int $issue_id): static
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    public function setAssemblyId(int $assembly_id): static
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getKind(): KindEnum
    {
        return $this->kind;
    }

    public function setKind(KindEnum $kind): static
    {
        $this->kind = $kind;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'document_id' => $this->document_id,
            'issue_id' => $this->issue_id,
            'kind' => $this->kind->value,
            'assembly_id' => $this->assembly_id,
            'date' => $this->date?->format('Y-m-d H:i:s'),
            'url' => $this->url,
            'type' => $this->type,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
