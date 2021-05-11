<?php

namespace Althingi\Model;

use DateTime;

class IssueProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Issue */
    private $issue;

    /** @var  \Althingi\Model\CongressmanPartyProperties[] */
    private $proponents;

    /** @var  \Althingi\Model\DateAndCount[] */
    private $voteRange;

    /** @var  \Althingi\Model\DateAndCount[] */
    private $speechRange;

    /** @var  \Althingi\Model\CongressmanAndDateRange[] */
    private $speakers;

    /** @var DateTime */
    private $date;

    /** @var bool */
    private $governmentIssue = false;

    /** @var string | null  */
    private $document_type = null;

    /** @var string | null  */
    private $document_url = null;

    /** @var \Althingi\Model\Category[] */
    private $categories;

    /** @var \Althingi\Model\SuperCategory[] */
    private $superCategory;

    /** @var int */
    private $speech_time = 0;

    /** @var int */
    private $speech_count = 0;

    /** @var \Althingi\Model\Link[] */
    private $links = [];

    /**
     * @return Issue
     */
    public function getIssue(): Issue
    {
        return $this->issue;
    }

    /**
     * @param Issue $issue
     * @return IssueProperties
     */
    public function setIssue(Issue $issue): self
    {
        $this->issue = $issue;
        return $this;
    }

    /**
     * @return array
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
     * @return IssueProperties
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
     * @return IssueProperties
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
     * @return IssueProperties
     */
    public function setSpeakers(array $speakers): self
    {
        $this->speakers = $speakers;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return IssueProperties
     */
    public function setDate(?DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return bool
     */
    public function isGovernmentIssue(): bool
    {
        return $this->governmentIssue;
    }

    /**
     * @param bool $governmentIssue
     * @return IssueProperties
     */
    public function setGovernmentIssue(bool $governmentIssue): self
    {
        $this->governmentIssue = $governmentIssue;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentType(): ?string
    {
        return $this->document_type;
    }

    /**
     * @param string|null $document_type
     * @return IssueProperties
     */
    public function setDocumentType(?string $document_type): self
    {
        $this->document_type = $document_type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumentUrl(): ?string
    {
        return $this->document_url;
    }

    /**
     * @param string|null $document_url
     * @return IssueProperties
     */
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
     * @return IssueProperties
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
     * @return IssueProperties
     */
    public function setSuperCategory(array $superCategory): self
    {
        $this->superCategory = $superCategory;
        return $this;
    }


    /**
     * @return int
     */
    public function getSpeechTime(): ?int
    {
        return $this->speech_time;
    }

    /**
     * @param int $speech_time
     * @return IssueProperties
     */
    public function setSpeechTime(?int $speech_time): self
    {
        $this->speech_time = $speech_time;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpeechCount(): ?int
    {
        return $this->speech_count;
    }

    /**
     * @param int $speech_count
     * @return IssueProperties
     */
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
     * @return IssueProperties
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

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
