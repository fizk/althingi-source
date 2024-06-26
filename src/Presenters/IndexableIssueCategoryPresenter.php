<?php

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\IssueCategory;
use Laminas\Hydrator\HydratorInterface;

class IndexableIssueCategoryPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_issue-category';
    private const TYPE = 'issue-category';

    private HydratorInterface $hydrator;
    private IssueCategory $model;

    public function __construct(IssueCategory $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\IssueCategory());
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
            $this->model->getAssemblyId(),
            $this->model->getIssueId(),
            $this->model->getCategory(),
            $this->model->getCategoryId()
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
