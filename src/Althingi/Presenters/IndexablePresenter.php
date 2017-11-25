<?php

namespace Althingi\Presenters;

use Althingi\Model\ModelInterface;
use Zend\Hydrator\HydratorInterface;

interface IndexablePresenter
{
    public function setHydrator(HydratorInterface $hydrator): IndexablePresenter;

    public function getHydrator(): HydratorInterface;

    public function setModel(ModelInterface $model): IndexablePresenter;

    public function getModel(): ModelInterface;

    public function getIdentifier(): string;

    public function getType(): string;

    public function getIndex(): string;

    public function getData(): array;
}
