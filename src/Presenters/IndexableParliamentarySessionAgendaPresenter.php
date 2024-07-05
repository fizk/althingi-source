<?php

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\ParliamentarySessionAgenda;
use Althingi\Utils\HydratorInterface;

class IndexableParliamentarySessionAgendaPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_parliamentary-session-agenda';
    private const TYPE = 'parliamentary-session-agenda';

    private HydratorInterface $hydrator;
    private ParliamentarySessionAgenda $model;

    public function __construct(ParliamentarySessionAgenda $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\ParliamentarySessionAgenda());
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
