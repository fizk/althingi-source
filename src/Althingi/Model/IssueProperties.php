<?php

namespace Althingi\Model;

/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 11/27/16
 * Time: 3:33 PM
 */
class IssueProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Issue */
    private $issue;

    /** @var  \Althingi\Model\CongressmanPartyProperties */
    private $proponent;

    /** @var  \Althingi\Model\DateAndCount[] */
    private $voteRange;

    /** @var  \Althingi\Model\DateAndCount[] */
    private $speechRange;

    /** @var  \Althingi\Model\CongressmanAndDateRange[] */
    private $speakers;

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
     * @return CongressmanPartyProperties
     */
    public function getProponent(): CongressmanPartyProperties
    {
        return $this->proponent;
    }

    /**
     * @param CongressmanPartyProperties $proponent
     * @return IssueProperties
     */
    public function setProponent(CongressmanPartyProperties $proponent): IssueProperties
    {
        $this->proponent = $proponent;
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

    public function toArray()
    {
        return array_merge(
            $this->issue->toArray(),
            [
                'proponent' => $this->proponent,
                'voteRange' => $this->voteRange,
                'speechRange' => $this->speechRange,
                'speakers' => $this->speakers,
            ]
        );
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
