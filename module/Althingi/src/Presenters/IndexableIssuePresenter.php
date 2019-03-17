<?php
namespace Althingi\Presenters;

use Althingi\Model\Issue;
use Althingi\Model\ModelInterface;
use Zend\Hydrator\HydratorInterface;

class IndexableIssuePresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_issue';
    const TYPE = 'issue';

    /** @var  \Zend\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\Issue */
    private $model;

    public function __construct(Issue $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Issue());
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
        return "{$this->model->getAssemblyId()}-{$this->model->getIssueId()}-{$this->model->getCategory()}";
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
