<?php
namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\Vote;
use Laminas\Hydrator\HydratorInterface;

class IndexableVotePresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_vote';
    const TYPE = 'vote';

    private HydratorInterface $hydrator;
    private Vote $model;

    public function __construct(Vote $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Vote());
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
        return $this->model->getVoteId();
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
