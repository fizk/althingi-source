<?php
namespace Althingi\Presenters;

use Althingi\Model\Ministry;
use Althingi\Model\ModelInterface;
use Zend\Hydrator\HydratorInterface;

class IndexableMinistryPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_ministry';
    const TYPE = 'ministry';

    /** @var  \Zend\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\Ministry */
    private $model;

    public function __construct(Ministry $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\MinisterSitting());
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
        return (string) $this->model->getMinistryId();
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
