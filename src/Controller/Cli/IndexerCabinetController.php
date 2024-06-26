<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\Cabinet;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\IndexableCabinetPresenter;
use Althingi\Injector\{
    EventsAwareInterface,
    ServiceCabinetAwareInterface
};
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerCabinetController implements ServiceCabinetAwareInterface, EventsAwareInterface
{
    use EventService;

    private Cabinet $cabinetService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \Althingi\Model\Cabinet $model */
        foreach ($this->cabinetService->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexableCabinetPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setCabinetService(Cabinet $cabinet): self
    {
        $this->cabinetService = $cabinet;
        return $this;
    }
}
