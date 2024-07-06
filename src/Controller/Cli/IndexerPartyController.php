<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServicePartyAwareInterface
};
use Althingi\Presenters\IndexablePartyPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;
use Althingi\Service\Party;

class IndexerPartyController implements ServicePartyAwareInterface, EventsAwareInterface
{
    use EventService;

    private Party $partyService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->partyService->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexablePartyPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setPartyService(Party $party): static
    {
        $this->partyService = $party;
        return $this;
    }
}
