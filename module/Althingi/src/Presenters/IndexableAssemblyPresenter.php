<?php
namespace Althingi\Presenters;

use Althingi\Model\Assembly;
use Althingi\Model\ModelInterface;
use Zend\Hydrator\HydratorInterface;

class IndexableAssemblyPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_assembly';
    const TYPE = 'assembly';

    /** @var  \Zend\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\Assembly */
    private $model;

    public function __construct(Assembly $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Assembly());
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
