<?php

namespace Althingi\Model;

class IssueWithSpeechProperties implements ModelInterface
{
    /** @var  \Althingi\Model\SpeechCongressmanProperties */
    private $speech;

    /** @var  \Althingi\Model\IssueAndDate */
    private $issue;

    /**
     * @return SpeechCongressmanProperties
     */
    public function getSpeech(): SpeechCongressmanProperties
    {
        return $this->speech;
    }

    /**
     * @param SpeechCongressmanProperties $speech
     * @return IssueWithSpeechProperties
     */
    public function setSpeech(SpeechCongressmanProperties $speech): self
    {
        $this->speech = $speech;
        return $this;
    }

    /**
     * @return IssueAndDate
     */
    public function getIssue(): IssueAndDate
    {
        return $this->issue;
    }

    /**
     * @param IssueAndDate $issue
     * @return IssueWithSpeechProperties
     */
    public function setIssue(IssueAndDate $issue): self
    {
        $this->issue = $issue;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge($this->issue->toArray(), [
            'speech' => $this->speech
        ]);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
