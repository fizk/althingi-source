<?php

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\PlenaryAgenda;
use Laminas\Hydrator\HydratorInterface;

class IndexablePlenaryAgendaPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_plenary-agenda';
    private const TYPE = 'plenary-agenda';

    private HydratorInterface $hydrator;
    private PlenaryAgenda $model;

    public function __construct(PlenaryAgenda $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\PlenaryAgenda());
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
