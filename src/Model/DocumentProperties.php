<?php

namespace Althingi\Model;

class DocumentProperties implements ModelInterface
{
    /** @var  \Althingi\Model\Document */
    private $document;

    /** @var  \Althingi\Model\Vote[] */
    private $votes;

    /** @var  \Althingi\Model\Proponent[] */
    private $proponents;

    /**
     * @return Document
     */
    public function getDocument(): Document
    {
        return $this->document;
    }

    /**
     * @param Document $document
     * @return DocumentProperties
     */
    public function setDocument(Document $document): DocumentProperties
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
     * @return DocumentProperties
     */
    public function setVotes(array $votes): DocumentProperties
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
     * @return DocumentProperties
     */
    public function setProponents(array $proponents): DocumentProperties
    {
        $this->proponents = $proponents;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(
            $this->document->toArray(),
            [
                'votes' => $this->votes,
                'proponents' => $this->proponents,
            ]
        );
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
