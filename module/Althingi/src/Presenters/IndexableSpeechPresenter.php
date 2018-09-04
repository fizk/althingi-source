<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 10/21/17
 * Time: 4:28 PM
 */

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Althingi\Model\Speech;
use Zend\Hydrator\HydratorInterface;

class IndexableSpeechPresenter implements IndexablePresenter
{
    const INDEX = 'althingi_model_speech';
    const TYPE = 'althingi_model_speech';

    /** @var  \Zend\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\Speech */
    private $model;

    public function __construct(Speech $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Speech());
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
        return $this->model->getSpeechId();
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
