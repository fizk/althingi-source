<?php

namespace Althingi\Presenters;

use Althingi\Model\CommitteeSession;
use Althingi\Hydrator;
use Althingi\Model\ModelInterface;
use Althingi\Utils\HydratorInterface;

class IndexableCommitteeSessionPresenter implements IndexablePresenter
{
    private const INDEX = 'althingi_model_committee-session';
    private const TYPE = 'committee-session';

    private HydratorInterface $hydrator;

    /** @var  \Althingi\Model\CommitteeSession */
    private CommitteeSession $model;

    public function __construct(CommitteeSession $model)
    {
        $this->setHydrator(new Hydrator\CommitteeSession());
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

    public function getModel(): CommitteeSession
    {
        return $this->model;
    }

    public function getIdentifier(): string
    {
        return $this->model->getCommitteeSessionId();
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
