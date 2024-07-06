<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\CommitteeSession;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceCommitteeSessionAwareInterface
};
use Althingi\Presenters\IndexableCommitteeSessionPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerCommitteeSessionController implements ServiceCommitteeSessionAwareInterface, EventsAwareInterface
{
    use EventService;

    private CommitteeSession $committeeSessionService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $assemblyId = $request->getAttribute('assembly_id', null);
        $congressmanId = $request->getAttribute('congressman_id', null);
        $committeeId = $request->getAttribute('committee_id', null);

        foreach (
            $this->committeeSessionService->fetchAllGenerator(
                $assemblyId,
                $congressmanId,
                $committeeId
            ) as $model
        ) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableCommitteeSessionPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setCommitteeSession(CommitteeSession $committeeSession): static
    {
        $this->committeeSessionService = $committeeSession;
        return $this;
    }
}
