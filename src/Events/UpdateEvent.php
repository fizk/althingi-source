<?php

namespace Althingi\Events;

use Althingi\Presenters\{
    IndexablePresenter,
    IndexablePresenterAwareInterface
};

class UpdateEvent implements EventInterface, IndexablePresenterAwareInterface
{
    private IndexablePresenter $presenter;
    private string $name;
    private array $params;

    public function __construct(IndexablePresenter $presenter, $params = [])
    {
        $this->setName('update');
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

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): static
    {
        $this->params = $params;
        return $this;
    }

    public function __toString()
    {
        return json_encode([
            'section_name' => 'query',
            'request_method' => 'UpdateEvent',
            'request_headers' => [],
            'request_uri' => implode('/', [
                get_class($this->presenter->getModel()),
                $this->presenter->getType(),
                $this->presenter->getIndex(),
                $this->presenter->getIdentifier(),
            ]),
            'response_status' => 200,
            'response_headers' => [],
            'error_file' => null,
            'error_message' => null,
            'error_trace' => null,
        ]);
    }
}
