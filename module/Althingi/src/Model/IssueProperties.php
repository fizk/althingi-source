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
    public function setIssue(Issue $issue): IssueProperties
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
    public function setProponents(array $proponents): IssueProperties
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
    public function setVoteRange(array $voteRange): IssueProperties
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
    public function setSpeechRange(array $speechRange): IssueProperties
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
    public function setSpeakers(array $speakers): IssueProperties
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
    public function setDate(?DateTime $date): IssueProperties
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
    public function setGovernmentIssue(bool $governmentIssue): IssueProperties
    {
        $this->governmentIssue = $governmentIssue;
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
    public function setCategories(array $categories): IssueProperties
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
    public function setSuperCategory(array $superCategory): IssueProperties
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
    public function setSpeechTime(?int $speech_time): IssueProperties
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
    public function setSpeechCount(?int $speech_count): IssueProperties
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
    public function setLinks(array $links): IssueProperties
    {
        $this->links = $links;
        return $this;
    }

    public function toArray()
    {
        return array_merge(
            $this->issue->toArray(),
            [
                'speech_time' => $this->speech_time,
                'speech_count' => $this->speech_count,
                'date' => $this->date ? $this->date->format('c') : null,
                'proponents' => $this->proponents,
                'speakers' => $this->speakers,
                'government_issue' => $this->governmentIssue,
                'categories' => $this->categories,
                'super_categories' => $this->superCategory,
                'issue_links' => $this->links,
            ]
        );
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
