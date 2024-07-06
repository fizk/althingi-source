<?php

namespace Althingi\Controller\Cli;

use Althingi\Service\President;
use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Injector\{
    EventsAwareInterface,
    ServicePresidentAwareInterface
};
use Althingi\Presenters\IndexablePresidentPresenter;
use Psr\Http\Message\{
    ServerRequestInterface,
    ResponseInterface
};
use Althingi\Service\EventService;

class IndexerPresidentController implements ServicePresidentAwareInterface, EventsAwareInterface
{
    use EventService;

    private President $presidentService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->presidentService->fetchAllGenerator() as $model) {
            $this->getEventDispatcher()->dispatch(
                new AddEvent(new IndexablePresidentPresenter($model), ['rows' => 1]),
            );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setPresidentService(President $president): static
    {
        $this->presidentService = $president;
        return $this;
    }
}
