<?php

namespace Althingi\Presenters;

use Althingi\Model\IssueLink;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableIssueLinkPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_issue-link';
    private const TYPE = 'issue-link';

    private HydratorInterface $hydrator;
    private IssueLink $model;

    public function __construct(IssueLink $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\IssueLink());
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
            $this->model->getKind()->value,
            $this->model->getFromAssemblyId(),
            $this->model->getFromIssueId(),
            $this->model->getFromKind()->value,
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
