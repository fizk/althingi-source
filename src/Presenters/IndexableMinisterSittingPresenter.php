<?php

namespace Althingi\Presenters;

use Althingi\Model\MinisterSitting;
use Althingi\Model\ModelInterface;
use Laminas\Hydrator\HydratorInterface;

class IndexableMinisterSittingPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_minister-sitting';
    private const TYPE = 'minister-sitting';

    private HydratorInterface $hydrator;
    private MinisterSitting $model;

    public function __construct(MinisterSitting $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\MinisterSitting());
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
        return (string) $this->model->getMinisterSittingId();
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
