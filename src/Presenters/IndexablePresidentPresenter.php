<?php
namespace Althingi\Presenters;

use Althingi\Model\Assembly;
use Althingi\Model\ModelInterface;
use Althingi\Model\President;
use Laminas\Hydrator\HydratorInterface;

class IndexablePresidentPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_president';
    const TYPE = 'president';

    private HydratorInterface $hydrator;
    private President $model;

    public function __construct(President $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\President());
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
        return (string) $this->model->getPresidentId();
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
