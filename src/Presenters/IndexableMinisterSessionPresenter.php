<?php

namespace Althingi\Presenters;

use Althingi\Model\MinisterSession;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableMinisterSessionPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_minister-session';
    private const TYPE = 'minister-session';

    private HydratorInterface $hydrator;
    private MinisterSession $model;

    public function __construct(MinisterSession $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\MinisterSession());
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
        return (string) $this->model->getMinisterSessionId();
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
