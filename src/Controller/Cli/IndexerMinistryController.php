<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\Ministry;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\IndexableMinistryPresenter;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceMinistryAwareInterface
};
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};

use Althingi\Service\EventService;

class IndexerMinistryController implements ServiceMinistryAwareInterface, EventsAwareInterface
{
    use EventService;
    private Ministry $ministryService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->ministryService->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableMinistryPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setMinistryService(Ministry $ministry): self
    {
        $this->ministryService = $ministry;
        return $this;
    }
}
