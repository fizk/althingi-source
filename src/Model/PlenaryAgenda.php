<?php
namespace Althingi\Model;

class PlenaryAgenda implements ModelInterface
{
    private $itemId;
    private int $plenaryId;
    private int $issueId;
    private int $assemblyId;
    private string $category;
    private ?string $iterationType = null;
    private ?string $iterationContinue = null;
    private ?string $iterationComment = null;
    private ?string $comment = null;
    private ?string $commentType = null;
    private ?int $posedId = null;
    private ?string $posed = null;
    private ?int $answererId = null;
    private ?string $answerer = null;
    private ?int $counterAnswererId = null;
    private ?string $counterAnswerer = null;
    private ?int $instigatorId = null;
    private ?string $instigator = null;

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function setItemId(int $itemId): self
    {
        $this->itemId = $itemId;
        return $this;
    }

    public function getPlenaryId(): int
    {
        return $this->plenaryId;
    }

    public function setPlenaryId(int $plenaryId): self
    {
        $this->plenaryId = $plenaryId;
        return $this;
    }

    public function getIssueId(): int
    {
        return $this->issueId;
    }

    public function setIssueId(int $issueId): self
    {
        $this->issueId = $issueId;
        return $this;
    }

    public function getAssemblyId(): int
    {
        return $this->assemblyId;
    }

    public function setAssemblyId(int $assemblyId): self
    {
        $this->assemblyId = $assemblyId;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getIterationType(): ?string
    {
        return $this->iterationType;
    }

    public function setIterationType(?string $iterationType): self
    {
        $this->iterationType = $iterationType;
        return $this;
    }

    public function getIterationContinue(): ?string
    {
        return $this->iterationContinue;
    }

    public function setIterationContinue(?string $iterationContinue): self
    {
        $this->iterationContinue = $iterationContinue;
        return $this;
    }

    public function getIterationComment(): ?string
    {
        return $this->iterationComment;
    }

    public function setIterationComment(?string $iterationComment): self
    {
        $this->iterationComment = $iterationComment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCommentType(): ?string
    {
        return $this->commentType;
    }

    public function setCommentType(?string $commentType): self
    {
        $this->commentType = $commentType;
        return $this;
    }

    public function getPosedId(): ?int
    {
        return $this->posedId;
    }

    public function setPosedId(?int $posedId): self
    {
        $this->posedId = $posedId;
        return $this;
    }

    public function getPosed(): ?string
    {
        return $this->posed;
    }

    public function setPosed(?string $posed): self
    {
        $this->posed = $posed;
        return $this;
    }

    public function getAnswererId(): ?int
    {
        return $this->answererId;
    }

    public function setAnswererId(?int $answererId): self
    {
        $this->answererId = $answererId;
        return $this;
    }

    public function getAnswerer(): ?string
    {
        return $this->answerer;
    }

    public function setAnswerer(?string $answerer): self
    {
        $this->answerer = $answerer;
        return $this;
    }

    public function getCounterAnswererId(): ?int
    {
        return $this->counterAnswererId;
    }

    public function setCounterAnswererId(?int $counterAnswererId): self
    {
        $this->counterAnswererId = $counterAnswererId;
        return $this;
    }

    public function getCounterAnswerer(): ?string
    {
        return $this->counterAnswerer;
    }

    public function setCounterAnswerer(?string $counterAnswerer): self
    {
        $this->counterAnswerer = $counterAnswerer;
        return $this;
    }

    public function getInstigatorId(): ?int
    {
        return $this->instigatorId;
    }

    public function setInstigatorId(?int $instigatorId): self
    {
        $this->instigatorId = $instigatorId;
        return $this;
    }

    public function getInstigator(): ?string
    {
        return $this->instigator;
    }

    public function setInstigator(?string $instigator): self
    {
        $this->instigator = $instigator;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'item_id' => $this->itemId,
            'plenary_id' => $this->plenaryId,
            'issue_id' => $this->issueId,
            'assembly_id' => $this->assemblyId,
            'category' => $this->category,
            'iteration_type' => $this->iterationType,
            'iteration_continue' => $this->iterationContinue,
            'iteration_comment' => $this->iterationComment,
            'comment' => $this->comment,
            'comment_type' => $this->commentType,
            'posed_id' => $this->posedId,
            'posed' => $this->posed,
            'answerer_id' => $this->answererId,
            'answerer' => $this->answerer,
            'counter_answerer_id' => $this->counterAnswererId,
            'counter_answerer' => $this->counterAnswerer,
            'instigator_id' => $this->instigatorId,
            'instigator' => $this->instigator,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
