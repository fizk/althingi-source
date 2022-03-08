<?php
namespace Althingi\Presenters;

use Althingi\Model\Inflation;
use Althingi\Model\ModelInterface;
use Laminas\Hydrator\HydratorInterface;

class IndexableInflationPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_inflation';
    const TYPE = 'inflation';

    private HydratorInterface $hydrator;
    private Inflation $model;

    public function __construct(Inflation $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Inflation());
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
        return "{$this->model->getId()}";
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
