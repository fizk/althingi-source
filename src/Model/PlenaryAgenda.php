<?php
namespace Althingi\Model;

class PlenaryAgenda implements ModelInterface
{
    /** @var int */
    private $itemId;

    /** @var int */
    private $plenaryId;

    /** @var int */
    private $issueId;

    /** @var int */
    private $assemblyId;

    /** @var string */
    private $category;

    /** @var string */
    private $iterationType = null;

    /** @var string */
    private $iterationContinue = null;

    /** @var string */
    private $iterationComment = null;

    /** @var string */
    private $comment = null;

    /** @var string */
    private $commentType = null;

    /** @var int */
    private $posedId = null;

    /** @var string */
    private $posed = null;

    /** @var int */
    private $answererId = null;

    /** @var string */
    private $answerer = null;

    /** @var int */
    private $counterAnswererId = null;

    /** @var string */
    private $counterAnswerer = null;

    /** @var int */
    private $instigatorId = null;

    /** @var string */
    private $instigator = null;

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->itemId;
    }

    /**
     * @param int $itemId
     * @return PlenaryAgenda
     */
    public function setItemId(int $itemId): PlenaryAgenda
    {
        $this->itemId = $itemId;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlenaryId(): int
    {
        return $this->plenaryId;
    }

    /**
     * @param int $plenaryId
     * @return PlenaryAgenda
     */
    public function setPlenaryId(int $plenaryId): PlenaryAgenda
    {
        $this->plenaryId = $plenaryId;
        return $this;
    }

    /**
     * @return int
     */
    public function getIssueId(): int
    {
        return $this->issueId;
    }

    /**
     * @param int $issueId
     * @return PlenaryAgenda
     */
    public function setIssueId(int $issueId): PlenaryAgenda
    {
        $this->issueId = $issueId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAssemblyId(): int
    {
        return $this->assemblyId;
    }

    /**
     * @param int $assemblyId
     * @return PlenaryAgenda
     */
    public function setAssemblyId(int $assemblyId): PlenaryAgenda
    {
        $this->assemblyId = $assemblyId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return PlenaryAgenda
     */
    public function setCategory(string $category): PlenaryAgenda
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getIterationType(): ?string
    {
        return $this->iterationType;
    }

    /**
     * @param string $iterationType
     * @return PlenaryAgenda
     */
    public function setIterationType(?string $iterationType): PlenaryAgenda
    {
        $this->iterationType = $iterationType;
        return $this;
    }

    /**
     * @return string
     */
    public function getIterationContinue(): ?string
    {
        return $this->iterationContinue;
    }

    /**
     * @param string $iterationContinue
     * @return PlenaryAgenda
     */
    public function setIterationContinue(?string $iterationContinue): PlenaryAgenda
    {
        $this->iterationContinue = $iterationContinue;
        return $this;
    }

    /**
     * @return string
     */
    public function getIterationComment(): ?string
    {
        return $this->iterationComment;
    }

    /**
     * @param string $iterationComment
     * @return PlenaryAgenda
     */
    public function setIterationComment(?string $iterationComment): PlenaryAgenda
    {
        $this->iterationComment = $iterationComment;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return PlenaryAgenda
     */
    public function setComment(?string $comment): PlenaryAgenda
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommentType(): ?string
    {
        return $this->commentType;
    }

    /**
     * @param string $commentType
     * @return PlenaryAgenda
     */
    public function setCommentType(?string $commentType): PlenaryAgenda
    {
        $this->commentType = $commentType;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosedId(): ?int
    {
        return $this->posedId;
    }

    /**
     * @param int $posedId
     * @return PlenaryAgenda
     */
    public function setPosedId(?int $posedId): PlenaryAgenda
    {
        $this->posedId = $posedId;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosed(): ?string
    {
        return $this->posed;
    }

    /**
     * @param string $posed
     * @return PlenaryAgenda
     */
    public function setPosed(?string $posed): PlenaryAgenda
    {
        $this->posed = $posed;
        return $this;
    }

    /**
     * @return int
     */
    public function getAnswererId(): ?int
    {
        return $this->answererId;
    }

    /**
     * @param int $answererId
     * @return PlenaryAgenda
     */
    public function setAnswererId(?int $answererId): PlenaryAgenda
    {
        $this->answererId = $answererId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnswerer(): ?string
    {
        return $this->answerer;
    }

    /**
     * @param string $answerer
     * @return PlenaryAgenda
     */
    public function setAnswerer(?string $answerer): PlenaryAgenda
    {
        $this->answerer = $answerer;
        return $this;
    }

    /**
     * @return int
     */
    public function getCounterAnswererId(): ?int
    {
        return $this->counterAnswererId;
    }

    /**
     * @param int $counterAnswererId
     * @return PlenaryAgenda
     */
    public function setCounterAnswererId(?int $counterAnswererId): PlenaryAgenda
    {
        $this->counterAnswererId = $counterAnswererId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCounterAnswerer(): ?string
    {
        return $this->counterAnswerer;
    }

    /**
     * @param string $counterAnswerer
     * @return PlenaryAgenda
     */
    public function setCounterAnswerer(?string $counterAnswerer): PlenaryAgenda
    {
        $this->counterAnswerer = $counterAnswerer;
        return $this;
    }

    /**
     * @return int
     */
    public function getInstigatorId(): ?int
    {
        return $this->instigatorId;
    }

    /**
     * @param int $instigatorId
     * @return PlenaryAgenda
     */
    public function setInstigatorId(?int $instigatorId): PlenaryAgenda
    {
        $this->instigatorId = $instigatorId;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstigator(): ?string
    {
        return $this->instigator;
    }

    /**
     * @param string $instigator
     * @return PlenaryAgenda
     */
    public function setInstigator(?string $instigator): PlenaryAgenda
    {
        $this->instigator = $instigator;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
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

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
