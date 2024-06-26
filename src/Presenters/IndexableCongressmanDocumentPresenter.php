<?php

namespace Althingi\Presenters;

use Althingi\Model\CongressmanDocument;
use Althingi\Model\ModelInterface;
use Laminas\Hydrator\HydratorInterface;

class IndexableCongressmanDocumentPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_congressman-document';
    private const TYPE = 'congressman-document';

    private HydratorInterface $hydrator;
    private CongressmanDocument $model;

    public function __construct(CongressmanDocument $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\CongressmanDocument());
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
            $this->model->getDocumentId(),
            $this->model->getCongressmanId()
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
