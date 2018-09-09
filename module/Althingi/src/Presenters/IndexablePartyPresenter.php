<?php
namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\Party;
use Zend\Hydrator\HydratorInterface;

class IndexablePartyPresenter implements IndexablePresenter
{
    /** @var  \Zend\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\Party */
    private $model;

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
        return strtolower(str_replace('\\', '_', get_class($this->model)));
    }

    public function getIndex(): string
    {
        return strtolower(str_replace('\\', '_', get_class($this->model)));
    }

    public function getData(): array
    {
        return $this->getHydrator()->extract($this->model);
    }
}
