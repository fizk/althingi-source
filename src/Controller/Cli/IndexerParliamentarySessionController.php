<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceParliamentarySessionAwareInterface
};
use Althingi\Presenters\IndexableParliamentarySessionPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;
use Althingi\Service\ParliamentarySession;

class IndexerParliamentarySessionController implements ServiceParliamentarySessionAwareInterface, EventsAwareInterface
{
    use EventService;

    private ParliamentarySession $parliamentarySessionService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);

        /** @var \Althingi\Model\ParliamentarySession $model */
        foreach ($this->parliamentarySessionService->fetchAllGenerator($assemblyId) as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableParliamentarySessionPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setParliamentarySession(ParliamentarySession $parliamentarySession): static
    {
        $this->parliamentarySessionService = $parliamentarySession;
        return $this;
    }
}
