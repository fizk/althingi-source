<?php

namespace Althingi\Events;

use Althingi\Presenters\{
    IndexablePresenter,
    IndexablePresenterAwareInterface
};

class DeleteEvent implements EventInterface, IndexablePresenterAwareInterface
{
    private IndexablePresenter $presenter;
    private string $name;
    private array $params;

    public function __construct(IndexablePresenter $presenter, $params = [])
    {
        $this->setName('delete');
        $this->setParams($params);
        $this->setPresenter($presenter);
    }

    public function getPresenter(): IndexablePresenter
    {
        return $this->presenter;
    }

    public function setPresenter(IndexablePresenter $presenter): IndexablePresenterAwareInterface
    {
        $this->presenter = $presenter;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function __toString()
    {
        return implode(' ', [
            'DeleteEvent',
            $this->presenter->getIndex(),
            $this->presenter->getIdentifier(),
            $this->presenter->getType(),
            get_class($this->presenter->getModel()),
        ]);
    }
}
