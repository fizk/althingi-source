<?php

namespace Althingi\Model;

use DateTime;

class IssueProperties implements ModelInterface
{
    private Issue $issue;
    /** @var \Althingi\Model\CongressmanPartyProperties[] */
    private array $proponents;
    /** @var \Althingi\Model\DateAndCount[] */
    private array $voteRange;
    /** @var \Althingi\Model\DateAndCount[] */
    private array $speechRange;
    /** @var \Althingi\Model\CongressmanAndDateRange[] */
    private array $speakers;
    private DateTime $date;
    private bool $governmentIssue = false;
    private ?string $document_type = null;
    private ?string $document_url = null;
    /** @var \Althingi\Model\Category[] */
    private array $categories;
    /** @var \Althingi\Model\SuperCategory[] */
    private array $superCategory;
    private int $speech_time = 0;
    private int $speech_count = 0;
    private array $links = [];

    public function getIssue(): Issue
    {
        return $this->issue;
    }

    public function setIssue(Issue $issue): self
    {
        $this->issue = $issue;
        return $this;
    }

    /**
     * @return \Althingi\Model\CongressmanPartyProperties[]
     */
    public function getProponents(): array
    {
        return $this->proponents;
    }

    /**
     * @param CongressmanPartyProperties[] $proponents
     * @return IssueProperties
     */
    public function setProponents(array $proponents): self
    {
        $this->proponents = $proponents;
        return $this;
    }

    /**
     * @return DateAndCount[]
     */
    public function getVoteRange(): array
    {
        return $this->voteRange;
    }

    /**
     * @param DateAndCount[] $voteRange
     */
    public function setVoteRange(array $voteRange): self
    {
        $this->voteRange = $voteRange;
        return $this;
    }

    /**
     * @return DateAndCount[]
     */
    public function getSpeechRange(): array
    {
        return $this->speechRange;
    }

    /**
     * @param DateAndCount[] $speechRange
     */
    public function setSpeechRange(array $speechRange): self
    {
        $this->speechRange = $speechRange;
        return $this;
    }

    /**
     * @return CongressmanAndDateRange[]
     */
    public function getSpeakers(): array
    {
        return $this->speakers;
    }

    /**
     * @param CongressmanAndDateRange[] $speakers
     */
    public function setSpeakers(array $speakers): self
    {
        $this->speakers = $speakers;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function isGovernmentIssue(): bool
    {
        return $this->governmentIssue;
    }

    public function setGovernmentIssue(bool $governmentIssue): self
    {
        $this->governmentIssue = $governmentIssue;
        return $this;
    }

    public function getDocumentType(): ?string
    {
        return $this->document_type;
    }

    public function setDocumentType(?string $document_type): self
    {
        $this->document_type = $document_type;
        return $this;
    }

    public function getDocumentUrl(): ?string
    {
        return $this->document_url;
    }

    public function setDocumentUrl(?string $document_url): self
    {
        $this->document_url = $document_url;
        return $this;
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param Category[] $categories
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return SuperCategory[]
     */
    public function getSuperCategory(): array
    {
        return $this->superCategory;
    }

    /**
     * @param SuperCategory[] $superCategory
     */
    public function setSuperCategory(array $superCategory): self
    {
        $this->superCategory = $superCategory;
        return $this;
    }

    public function getSpeechTime(): ?int
    {
        return $this->speech_time;
    }

    public function setSpeechTime(?int $speech_time): self
    {
        $this->speech_time = $speech_time;
        return $this;
    }

    public function getSpeechCount(): ?int
    {
        return $this->speech_count;
    }

    public function setSpeechCount(?int $speech_count): self
    {
        $this->speech_count = $speech_count;
        return $this;
    }

    /**
     * @return Link[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param Link[] $links
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->issue->toArray(),
            [
                'speech_time' => $this->speech_time,
                'speech_count' => $this->speech_count,
                'date' => $this->date?->format('c'),
                'proponents' => $this->proponents,
                'speakers' => $this->speakers,
                'government_issue' => $this->governmentIssue,
                'document_type' => $this->document_type,
                'document_url' => $this->document_url,
                'categories' => $this->categories,
                'super_categories' => $this->superCategory,
                'issue_links' => $this->links,
            ]
        );
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
