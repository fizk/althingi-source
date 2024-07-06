<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceConstituencyAwareInterface,
};
use Althingi\Presenters\IndexableConstituencyPresenter;
use Althingi\Service\Constituency;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerConstituencyController implements ServiceConstituencyAwareInterface, EventsAwareInterface
{
    use EventService;

    private Constituency $constituencyService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->constituencyService->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableConstituencyPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setConstituencyService(Constituency $constituency): static
    {
        $this->constituencyService = $constituency;
        return $this;
    }
}
