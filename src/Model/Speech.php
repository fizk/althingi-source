<?php

namespace Althingi\Model;

use DateTime;

class Speech implements ModelInterface
{
    private $speech_id;
    private $plenary_id;
    private $assembly_id;
    private $issue_id;
    private $category;
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

    public function setSpeechId(string $speech_id): self
    {
        $this->speech_id = $speech_id;
        return $this;
    }

    public function getPlenaryId(): int
    {
        return $this->plenary_id;
    }

    public function setPlenaryId(int $plenary_id): self
    {
        $this->plenary_id = $plenary_id;
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

    public function getIssueId(): int
    {
        return $this->issue_id;
    }

    public function setIssueId(int $issue_id): self
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
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

    public function getCongressmanType(): ?string
    {
        return $this->congressman_type;
    }

    public function setCongressmanType(?string $congressman_type): self
    {
        $this->congressman_type = $congressman_type;
        return $this;
    }

    public function getFrom(): ?DateTime
    {
        return $this->from;
    }

    public function setFrom(?DateTime $from): self
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): ?DateTime
    {
        return $this->to;
    }

    public function setTo(?DateTime $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getIteration(): ?string
    {
        return $this->iteration;
    }

    public function setIteration(?string $iteration ): self
    {
        $this->iteration = $iteration;
        return $this;
    }

    public function getWordCount(): int
    {
        return $this->word_count;
    }

    public function setWordCount(?int $word_count): self
    {
        $this->word_count = $word_count ? : 0;
        return $this;
    }

    public function isValidated(): bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated = true): self
    {
        $this->validated = $validated;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'speech_id' => $this->speech_id,
            'plenary_id' => $this->plenary_id,
            'assembly_id' => $this->assembly_id,
            'issue_id' => $this->issue_id,
            'category' => $this->category,
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
