<?php

namespace Althingi\Controller;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Service\Assembly;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Althingi\Injector\EventsAwareInterface;

use Althingi\Service\EventService;

class IndexerAssemblyController implements ServiceAssemblyAwareInterface, EventsAwareInterface
{
    use EventService;
    private Assembly $assemblyService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->assemblyService->fetchAll() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableAssemblyPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setAssemblyService(Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }
}
