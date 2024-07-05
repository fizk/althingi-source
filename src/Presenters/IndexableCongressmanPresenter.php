<?php

namespace Althingi\Presenters;

use Althingi\Model\Congressman;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableCongressmanPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_congressman';
    private const TYPE = 'congressman';

    private HydratorInterface $hydrator;
    private Congressman $model;

    public function __construct(Congressman $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Congressman());
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
        return (string) $this->model->getCongressmanId();
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
