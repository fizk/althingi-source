<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\Assembly;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceAssemblyAwareInterface
};
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};

use Althingi\Service\EventService;

class IndexerAssemblyController implements ServiceAssemblyAwareInterface, EventsAwareInterface
{
    use EventService;
    private Assembly $assemblyService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->assemblyService->fetchAllGenerator() as $model) {
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
