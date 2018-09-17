<?php

namespace Althingi;

use Zend\Mvc\MvcEvent;
use Rend\Event\ApplicationErrorHandler;
use Rend\Event\ShutdownErrorHandler;
use Zend\Cache\Storage\StorageInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Request as PhpRequest;

class Module
{
    const VERSION = '3.0.3-dev';

    public function onBootstrap(MvcEvent $e)
    {
        register_shutdown_function(new ShutdownErrorHandler());

        $cache = $e->getApplication()->getServiceManager()->get(StorageInterface::class);

        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, new ApplicationErrorHandler());

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function (MvcEvent $event) use ($cache) {
            if ($event->getRequest() instanceof \Zend\Console\Request) {
                return;
            }
            $storageKey = $this->storageKey($event->getRequest());

            if ($event->getRequest()->getMethod() === HttpRequest::METHOD_GET && $cache->hasItem($storageKey)) {
                $event->getApplication()->getEventManager()->clearListeners(MvcEvent::EVENT_DISPATCH);
                $event->getResponse()->setHeaders((new \Zend\Http\Headers())->addHeaders([
                    'X-Cache' => 'HIT'
                ]));
                $event->setViewModel(unserialize($cache->getItem($storageKey)));
            }
        });

        $eventManager->attach(MvcEvent::EVENT_FINISH, function (MvcEvent $event) use ($cache) {
            if ($event->getRequest() instanceof \Zend\Console\Request) {
                return;
            }
            $storageKey = $this->storageKey($event->getRequest());

            if ($event->getRequest()->getMethod() === HttpRequest::METHOD_GET &&
                ! $cache->hasItem($storageKey) &&
                $event->getResponse()->isSuccess()) {
                $event->getResponse()->setHeaders((new \Zend\Http\Headers())->addHeaders([
                    'X-Cache' => 'MISS'
                ]));
                $cache->addItem($storageKey, serialize($event->getResult()));
            }
        });
    }

    private function storageKey(PhpRequest $request)
    {
        /** @var  $path \Zend\Uri\Http*/
        $uri = $request->getUri();
        $query = $uri->getQueryAsArray();
        ksort($query);

        $range = $request->getHeaders()->has('Range')
            ? $request->getHeaders()->get('Range')->getFieldValue()
            : '';
        return md5($uri->getPath() . $range . json_encode($query));
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
