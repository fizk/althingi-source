<?php
namespace Althingi\Presenters;

use Althingi\Model\Cabinet;
use Althingi\Model\ModelInterface;
use Laminas\Hydrator\HydratorInterface;

class IndexableCabinetPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_cabinet';
    const TYPE = 'cabinet';

    /** @var  \Laminas\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\Cabinet */
    private $model;

    public function __construct(Cabinet $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Cabinet());
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
        return "{$this->model->getCabinetId()}";
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
