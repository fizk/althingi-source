<?php

namespace Althingi\Presenters;

use Althingi\Model\Issue;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableIssuePresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_issue';
    private const TYPE = 'issue';

    private HydratorInterface $hydrator;
    private Issue $model;

    public function __construct(Issue $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Issue());
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
        return "{$this->model->getAssemblyId()}-{$this->model->getIssueId()}-{$this->model->getKind()->value}";
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
