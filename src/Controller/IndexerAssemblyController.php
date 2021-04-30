<?php

namespace Althingi\Controller;

use Althingi\Events\AddEvent;
use Althingi\Utils\ConsoleResponse;
use Althingi\Injector\ServiceAssemblyAwareInterface;
use Althingi\Presenters\IndexableAssemblyPresenter;
use Althingi\Service\Assembly;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Althingi\Injector\EventsAwareInterface;

class IndexerAssemblyController implements ServiceAssemblyAwareInterface, EventsAwareInterface
{
    private Assembly $assemblyService;
    protected ?EventManagerInterface $events = null;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->assemblyService->fetchAll() as $model) {
            $this->getEventManager()
                ->trigger(
                    AddEvent::class,
                    new AddEvent(new IndexableAssemblyPresenter($model)),
                    ['rows' => 1]
                );
        }

        return (new ConsoleResponse(__CLASS__));
    }

    public function setAssemblyService(Assembly $assembly): self
    {
        $this->assemblyService = $assembly;
        return $this;
    }

    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
        return $this;
    }

    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
