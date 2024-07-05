<?php

namespace Althingi\Presenters;

use Althingi\Model\Category;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableCategoryPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_category';
    private const TYPE = 'category';

    private HydratorInterface $hydrator;
    private Category $model;

    public function __construct(Category $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Category());
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
        return (string) $this->model->getCategoryId();
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
