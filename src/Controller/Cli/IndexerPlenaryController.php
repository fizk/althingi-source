<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServicePlenaryAwareInterface
};
use Althingi\Presenters\IndexablePlenaryPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};

use Althingi\Service\EventService;
use Althingi\Service\Plenary;

class IndexerPlenaryController implements ServicePlenaryAwareInterface, EventsAwareInterface
{
    use EventService;
    private Plenary $plenaryService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        /** @var \Althingi\Model\Plenary $model */
        foreach ($this->plenaryService->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexablePlenaryPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setPlenaryService(Plenary $plenary): self
    {
        $this->plenaryService = $plenary;
        return $this;
    }
}
