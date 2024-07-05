<?php

namespace Althingi\Model;

use DateTime;

class Speech implements ModelInterface
{
    private $speech_id;
    private $parliamentarySessionId;
    private $assembly_id;
    private $issue_id;
    private ?KindEnum $kind;
    private $congressman_id;
    private ?string $congressman_type = null;
    private ?DateTime $from = null;
    private ?DateTime $to = null;
    private ?string $text = null;
    private ?string $type = null;
    private ?string $iteration = null;
    private $word_count = 0;
    private $validated = true;

    public function getSpeechId(): string
    {
        return $this->speech_id;
    }

    public function setSpeechId(string $speech_id): static
    {
        $this->speech_id = $speech_id;
        return $this;
    }

    public function getParliamentarySessionId(): int
    {
        return $this->parliamentarySessionId;
    }

    public function setParliamentarySessionId(int $parliamentarySessionId): static
    {
        $this->parliamentarySessionId = $parliamentarySessionId;
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

    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    public function setIssueId(int $issue_id): static
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    public function getKind(): ?KindEnum
    {
        return $this->kind;
    }

    public function setKind(?KindEnum $kind): static
    {
        $this->kind = $kind;

        return $this;
    }

    public function getCongressmanId(): int
    {
        return $this->congressman_id;
    }

    public function setCongressmanId(int $congressman_id): static
    {
        $this->congressman_id = $congressman_id;
        return $this;
    }

    public function getCongressmanType(): ?string
    {
        return $this->congressman_type;
    }

    public function setCongressmanType(?string $congressman_type): static
    {
        $this->congressman_type = $congressman_type;
        return $this;
    }

    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    public function setFrom(?DateTime $from): static
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    public function setTo(?DateTime $to): static
    {
        $this->to = $to;
        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;
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

    public function getIteration(): ?string
    {
        return $this->iteration;
    }

    public function setIteration(?string $iteration): static
    {
        $this->iteration = $iteration;
        return $this;
    }

    public function getWordCount(): int
    {
        return $this->word_count;
    }

    public function setWordCount(?int $word_count): static
    {
        $this->word_count = $word_count ? : 0;
        return $this;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated = true): static
    {
        $this->validated = $validated;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'speech_id' => $this->speech_id,
            'parliamentary_session_id' => $this->parliamentarySessionId,
            'assembly_id' => $this->assembly_id,
            'issue_id' => $this->issue_id,
            'kind' => $this->kind->value,
            'congressman_id' => $this->congressman_id,
            'congressman_type' => $this->congressman_type,
            'from' => $this->from?->format('Y-m-d H:i:s'),
            'to' => $this->to?->format('Y-m-d H:i:s'),
            'text' => $this->text,
            'type' => $this->type,
            'iteration' => $this->iteration,
            'word_count' => $this->word_count,
            'validated' => $this->validated,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
