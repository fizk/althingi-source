<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\Inflation;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\IndexableInflationPresenter;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceInflationAwareInterface
};
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerInflationController implements ServiceInflationAwareInterface, EventsAwareInterface
{
    use EventService;

    private Inflation $inflationService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \Althingi\Model\Inflation $model */
        foreach ($this->inflationService->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableInflationPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setInflationService(Inflation $inflation): static
    {
        $this->inflationService = $inflation;
        return $this;
    }
}
