<?php
namespace Althingi\Presenters;

use Althingi\Model;
use Althingi\Hydrator;
use Zend\Hydrator\HydratorInterface;

class IndexableCommitteeSittingPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_committee-sitting';
    const TYPE = 'committee-sitting';

    /** @var  \Zend\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\CommitteeSitting */
    private $model;

    public function __construct(Model\CommitteeSitting $model)
    {
        $this->setHydrator(new Hydrator\CommitteeSitting());
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

    public function setModel(Model\ModelInterface $model): IndexablePresenter
    {
        $this->model = $model;
        return $this;
    }

    public function getModel(): Model\ModelInterface
    {
        return $this->model;
    }

    public function getIdentifier(): string
    {
        return $this->model->getCommitteeSittingId();
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
