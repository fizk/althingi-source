<?php

namespace Althingi\Presenters;

use Althingi\Model\Constituency;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableConstituencyPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_constituency';
    private const TYPE = 'constituency';

    private HydratorInterface $hydrator;
    private Constituency $model;

    public function __construct(Constituency $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Constituency());
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
        return "{$this->model->getConstituencyId()}";
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
