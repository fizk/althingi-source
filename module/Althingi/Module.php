<?php

namespace Althingi;

use Althingi\Utils\RequestCacheHandler;
use Althingi\Utils\RequestErrorHandler;
use Zend\Mvc\MvcEvent;
use Rend\Event\ShutdownErrorHandler;
use Zend\Cache\Storage\StorageInterface;

class Module
{
    const VERSION = '3.0.3-dev';

    public function onBootstrap(MvcEvent $event)
    {
        register_shutdown_function(new ShutdownErrorHandler());

        $eventManager = $event->getApplication()
            ->getEventManager();
        $storageInterface = $event->getApplication()
            ->getServiceManager()
            ->get(StorageInterface::class);

        (new RequestErrorHandler())
            ->attach($eventManager);

        (new RequestCacheHandler())
            ->setStorage($storageInterface)
            ->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }
}
