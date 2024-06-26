<?php

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\VoteItem;
use Laminas\Hydrator\HydratorInterface;

class IndexableVoteItemPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_vote-item';
    private const TYPE = 'vote-item';

    private HydratorInterface $hydrator;
    private VoteItem $model;

    public function __construct(VoteItem $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\VoteItem());
        $this->setModel($model);
    }

    public function setHydrator(HydratorInterface $hydrator): IndexablePresenter
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    public function getHydrator(): HydratorInterface
    {
        return $this->hydrator;
    }

    public function setModel(ModelInterface $model): IndexablePresenter
    {
        $this->model = $model;
        return $this;
    }

    public function getModel(): ModelInterface
    {
        return $this->model;
    }

    public function getIdentifier(): string
    {
        return implode('-', [
            $this->model->getVoteId(),
            $this->model->getVoteItemId(),
        ]);
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getIndex(): string
    {
        return self::INDEX;
    }

    public function getData(): array
    {
        return $this->getHydrator()->extract($this->model);
    }
}
