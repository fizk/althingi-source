<?php
namespace Althingi\Presenters;

use Althingi\Model\CommitteeSitting;
use Althingi\Hydrator;
use Althingi\Model\ModelInterface;
use Laminas\Hydrator\HydratorInterface;

class IndexableCommitteeSittingPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_committee-sitting';
    const TYPE = 'committee-sitting';

    private HydratorInterface $hydrator;

    /** @var  \Althingi\Model\CommitteeSitting */
    private CommitteeSitting $model;

    public function __construct(CommitteeSitting $model)
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

    public function setModel(ModelInterface $model): IndexablePresenter
    {
        $this->model = $model;
        return $this;
    }

    public function getModel(): CommitteeSitting
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
