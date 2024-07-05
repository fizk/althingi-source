<?php

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\Party;
use Althingi\Utils\HydratorInterface;

class IndexablePartyPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_party';
    private const TYPE = 'party';

    private HydratorInterface $hydrator;
    private Party $model;

    public function __construct(Party $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Party());
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
        return (string) $this->model->getPartyId();
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
