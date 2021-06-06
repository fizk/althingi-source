<?php
namespace Althingi\Presenters;

use Althingi\Model\Committee;
use Althingi\Hydrator;
use Althingi\Model\ModelInterface;
use Laminas\Hydrator\HydratorInterface;

class IndexableCommitteePresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_committee';
    const TYPE = 'committee';

    private HydratorInterface $hydrator;

    private Committee $model;

    public function __construct(Committee $model)
    {
        $this->setHydrator(new Hydrator\Committee());
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

    public function getModel(): Committee
    {
        return $this->model;
    }

    public function getIdentifier(): string
    {
        return $this->model->getCommitteeId();
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
