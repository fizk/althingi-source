<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceParliamentarySessionAgendaAwareInterface,
};
use Althingi\Presenters\IndexableParliamentarySessionAgendaPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;
use Althingi\Service\ParliamentarySessionAgenda;

class IndexerParliamentarySessionAgentaController implements
    ServiceParliamentarySessionAgendaAwareInterface,
    EventsAwareInterface
{
    use EventService;

    private ParliamentarySessionAgenda $parliamentarySessionAgendaService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        foreach ($this->parliamentarySessionAgendaService->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableParliamentarySessionAgendaPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setParliamentarySessionAgendaService(ParliamentarySessionAgenda $parliamentarySessionAgenda): static
    {
        $this->parliamentarySessionAgendaService = $parliamentarySessionAgenda;
        return $this;
    }
}
