<?php
namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\Session;
use Althingi\Model\Speech;
use Laminas\Hydrator\HydratorInterface;

class IndexableSessionPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_session';
    const TYPE = 'session';

    private HydratorInterface $hydrator;
    private Session $model;

    public function __construct(Session $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Session());
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
        return $this->model->getSessionId();
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
