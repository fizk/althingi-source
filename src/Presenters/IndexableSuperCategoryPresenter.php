<?php

namespace Althingi\Presenters;

use Althingi\Model\SuperCategory;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableSuperCategoryPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_super-category';
    private const TYPE = 'super-category';

    private HydratorInterface $hydrator;
    private SuperCategory $model;

    public function __construct(SuperCategory $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\SuperCategory());
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
        return (string) $this->model->getSuperCategoryId();
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
