<?php

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\ParliamentarySession;
use Althingi\Utils\HydratorInterface;

class IndexableParliamentarySessionPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_parliamentary-session';
    private const TYPE = 'parliamentary-session';

    private HydratorInterface $hydrator;
    private ParliamentarySession $model;

    public function __construct(ParliamentarySession $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\ParliamentarySession());
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
        return (string) $this->model->getAssemblyId();
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
