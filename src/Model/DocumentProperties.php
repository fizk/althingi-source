<?php

namespace Althingi\Model;

class DocumentProperties implements ModelInterface
{
    private Document $document;
    /** @var  \Althingi\Model\Vote[] */
    private array $votes = [];
    /** @var  \Althingi\Model\Proponent[] */
    private array $proponents = [];

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function setDocument(Document $document): self
    {
        $this->document = $document;
        return $this;
    }

    /**
     * @return Vote[]
     */
    public function getVotes(): array
    {
        return $this->votes;
    }

    /**
     * @param Vote[] $votes
     */
    public function setVotes(array $votes): self
    {
        $this->votes = $votes;
        return $this;
    }

    /**
     * @return Proponent[]
     */
    public function getProponents(): array
    {
        return $this->proponents;
    }

    /**
     * @param Proponent[] $proponents
     */
    public function setProponents(array $proponents): self
    {
        $this->proponents = $proponents;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->document->toArray(),
            [
                'votes' => $this->votes,
                'proponents' => $this->proponents,
            ]
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
