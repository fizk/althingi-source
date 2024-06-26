<?php

namespace Althingi\Model;

class CommitteeMeetingAgenda implements ModelInterface
{
    private $committee_meeting_agenda_id;
    private $committee_meeting_id;
    private ?int $issue_id = null;
    private int $assembly_id;
    private ?string $title = null;
    private ?KindEnum $kind;

    public function getCommitteeMeetingAgendaId(): int
    {
        return $this->committee_meeting_agenda_id;
    }

    public function setCommitteeMeetingAgendaId(int $committee_meeting_agenda_id): self
    {
        $this->committee_meeting_agenda_id = $committee_meeting_agenda_id;
        return $this;
    }

    public function getCommitteeMeetingId(): int
    {
        return $this->committee_meeting_id;
    }

    public function setCommitteeMeetingId(int $committee_meeting_id): self
    {
        $this->committee_meeting_id = $committee_meeting_id;
        return $this;
    }

    public function getIssueId(): ?int
    {
        return $this->issue_id;
    }

    public function setIssueId(?int $issue_id): self
    {
        $this->issue_id = $issue_id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getKind(): ?KindEnum
    {
        return $this->kind;
    }

    public function setKind(?KindEnum $kind): self
    {
        $this->kind = $kind;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'committee_meeting_agenda_id' => $this->committee_meeting_agenda_id,
            'committee_meeting_id' => $this->committee_meeting_id,
            'issue_id' => $this->issue_id,
            'kind' => $this->kind?->value ?? null,
            'assembly_id' => $this->assembly_id,
            'title' => $this->title,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
