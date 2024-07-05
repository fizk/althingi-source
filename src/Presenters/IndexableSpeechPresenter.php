<?php

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\Speech;
use Althingi\Utils\HydratorInterface;

class IndexableSpeechPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_speech';
    private const TYPE = 'speech';

    private HydratorInterface $hydrator;
    private Speech $model;

    public function __construct(Speech $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Speech());
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
        return $this->model->getSpeechId();
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
