<?php

namespace Althingi\Model;

class CommitteeMeetingAgenda implements ModelInterface
{
    /** @var  int */
    private $committee_meeting_agenda_id;

    /** @var  int */
    private $committee_meeting_id;

    /** @var  int */
    private $issue_id = null;

    /** @var  int */
    private $assembly_id;

    /** @var  string */
    private $title = null;

    /** @var  string */
    private $category;

    /**
     * @return int
     */
    public function getCommitteeMeetingAgendaId(): int
    {
        return $this->committee_meeting_agenda_id;
    }

    /**
     * @param int $committee_meeting_agenda_id
     * @return CommitteeMeetingAgenda
     */
    public function setCommitteeMeetingAgendaId(int $committee_meeting_agenda_id): CommitteeMeetingAgenda
    {
        $this->committee_meeting_agenda_id = $committee_meeting_agenda_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getCommitteeMeetingId(): int
    {
        return $this->committee_meeting_id;
    }

    /**
     * @param int $committee_meeting_id
     * @return CommitteeMeetingAgenda
     */
    public function setCommitteeMeetingId(int $committee_meeting_id): CommitteeMeetingAgenda
    {
        $this->committee_meeting_id = $committee_meeting_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getIssueId(): ?int
    {
        return $this->issue_id;
    }

    /**
     * @param int $issue_id
     * @return CommitteeMeetingAgenda
     */
    public function setIssueId(int $issue_id = null): CommitteeMeetingAgenda
    {
        $this->issue_id = $issue_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assembly_id;
    }

    /**
     * @param int $assembly_id
     * @return CommitteeMeetingAgenda
     */
    public function setAssemblyId(int $assembly_id): CommitteeMeetingAgenda
    {
        $this->assembly_id = $assembly_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return CommitteeMeetingAgenda
     */
    public function setTitle(string $title = null): CommitteeMeetingAgenda
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return CommitteeMeetingAgenda
     */
    public function setCategory(?string $category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'committee_meeting_agenda_id' => $this->committee_meeting_agenda_id,
            'committee_meeting_id' => $this->committee_meeting_id,
            'issue_id' => $this->issue_id,
            'category' => $this->category,
            'assembly_id' => $this->assembly_id,
            'title' => $this->title,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
