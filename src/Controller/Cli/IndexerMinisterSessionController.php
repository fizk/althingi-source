<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\MinisterSession;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceMinisterSessionAwareInterface
};
use Althingi\Presenters\IndexableMinisterSessionPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerMinisterSessionController implements ServiceMinisterSessionAwareInterface, EventsAwareInterface
{
    use EventService;

    private MinisterSession $ministerSessionService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        foreach ($this->ministerSessionService->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableMinisterSessionPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setMinisterSessionService(MinisterSession $ministerSession): static
    {
        $this->ministerSessionService = $ministerSession;
        return $this;
    }
}
