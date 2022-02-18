<?php

namespace Althingi\Form;

use Althingi\Model\ModelInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\InputFilter\InputFilterInterface;

abstract class Form implements InputFilterProviderInterface
{
    private string $name;
    private ModelInterface $model;
    private ?ModelInterface $object = null;
    private HydratorInterface $hydrator;
    private array $data = [];
    private ?bool $valid = null;
    private Factory $factory;
    private InputFilterInterface $inputFilter;

    public function __construct(string $name)
    {
        $this->factory = new Factory();
        $this->name = $name;
    }

    public function setObject(ModelInterface $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function setHydrator(HydratorInterface $hydrator): self
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    public function bind(ModelInterface $object): self
    {
        $this->object = $object;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getObject(): ModelInterface
    {
        if ($this->valid === null) {
            throw new \Exception('isValid needs to be called first');
        }
        return $this->model;
    }

    public function isValid(): bool
    {
        if ($this->valid !== null) {
            return $this->valid;
        }


        $this->inputFilter = $this->factory->createInputFilter(
            $this->getInputFilterSpecification()
        );

        $tmpData = $this->data;
        if ($this->object) {
            $tmpData = array_merge($this->object->toArray(), $tmpData);
        }

        $this->inputFilter->setData($tmpData);


        $this->valid = $this->inputFilter->isValid();

        if ($this->valid === false) {
            return false;
        }

        $this->model = $this->hydrator->hydrate($this->inputFilter->getValues(), $this->model);

        return $this->valid;
    }

    public function getMessages(): array
    {
        return $this->inputFilter
            ? $this->inputFilter?->getMessages()
            : [];
    }

    abstract public function getInputFilterSpecification(): array;
}
