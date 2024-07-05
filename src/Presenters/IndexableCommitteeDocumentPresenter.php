<?php

namespace Althingi\Presenters;

use Althingi\Model\CommitteeDocument;
use Althingi\Hydrator;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableCommitteeDocumentPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_committee-document';
    private const TYPE = 'committee-document';

    private HydratorInterface $hydrator;

    private CommitteeDocument $model;

    public function __construct(CommitteeDocument $model)
    {
        $this->setHydrator(new Hydrator\Committee());
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

    public function getModel(): CommitteeDocument
    {
        return $this->model;
    }

    public function getIdentifier(): string
    {
        return $this->model->getDocumentCommitteeId();
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
