<?php

namespace Althingi\Controller\Cli;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceCommitteeAwareInterface
};
use Althingi\Presenters\IndexableCommitteePresenter;
use Althingi\Service\Committee;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerCommitteeController implements ServiceCommitteeAwareInterface, EventsAwareInterface
{
    use EventService;

    private Committee $committeeService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->committeeService->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableCommitteePresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setCommitteeService(Committee $committee): static
    {
        $this->committeeService = $committee;
        return $this;
    }
}
