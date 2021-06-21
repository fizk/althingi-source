<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServicePlenaryAgendaAwareInterface,
};
use Althingi\Presenters\IndexablePlenaryAgendaPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};

use Althingi\Service\EventService;
use Althingi\Service\PlenaryAgenda;

class IndexerPlenaryAgentaController implements ServicePlenaryAgendaAwareInterface, EventsAwareInterface
{
    use EventService;
    private PlenaryAgenda $plenaryAgendaService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        foreach ($this->plenaryAgendaService->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexablePlenaryAgendaPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setPlenaryAgendaService(PlenaryAgenda $plenaryAgenda): self
    {
        $this->plenaryAgendaService = $plenaryAgenda;
        return $this;
    }
}
