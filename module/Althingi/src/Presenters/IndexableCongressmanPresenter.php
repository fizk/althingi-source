<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 10/21/17
 * Time: 4:28 PM
 */

namespace Althingi\Presenters;

use Althingi\Model\Congressman;
use Althingi\Model\ModelInterface;
use Zend\Hydrator\HydratorInterface;

class IndexableCongressmanPresenter implements IndexablePresenter
{
    /** @var  \Zend\Hydrator\HydratorInterface; */
    private $hydrator;

    /** @var  \Althingi\Model\Congressman */
    private $model;

    public function __construct(Congressman $model)
    {
        $this->setHydrator(new \Althingi\Hydrator\Congressman());
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
        return (string) $this->model->getCongressmanId();
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
