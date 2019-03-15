<?php
namespace Althingi\Presenters;

use Althingi\Model\Document;
use Althingi\Model\ModelInterface;
use Zend\Hydrator\HydratorInterface;

class IndexableDocumentPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_document';
    const TYPE = 'document';

    /** @var  \Zend\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\Document */
    private $model;

    public function __construct(Document $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Document());
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
        return implode('-', [
            $this->model->getAssemblyId(),
            $this->model->getIssueId(),
            $this->model->getDocumentId()
        ]);
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
