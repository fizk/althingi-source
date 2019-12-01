<?php
namespace Althingi\Utils;

use Althingi\Injector\CacheAwareInterface;
use Althingi\Injector\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Zend\Cache\Storage\StorageInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Request as PhpRequest;
use Zend\Mvc\MvcEvent;

class RequestCacheHandler implements ListenerAggregateInterface, CacheAwareInterface, LoggerAwareInterface
{
    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    private $storage;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $cache = $this->storage;
        $events->attach(MvcEvent::EVENT_ROUTE, function (MvcEvent $event) use ($cache) {
            if ($event->getRequest() instanceof \Zend\Console\Request) {
                return;
            }

            try {
                $storageKey = $this->storageKey($event->getRequest());
                if ($event->getRequest()->getMethod() === HttpRequest::METHOD_GET &&
                    $cache->hasItem($storageKey) &&
                    $cache->getItem($storageKey) !== null
                ) {
                    $event->getApplication()->getEventManager()->clearListeners(MvcEvent::EVENT_DISPATCH);
                    $event->getResponse()->getHeaders()->addHeaderLine("X-Cache: HIT");
                    $event->setViewModel(unserialize($cache->getItem($storageKey)));
                }
            } catch (\Throwable $e) {
                $event->getResponse()->getHeaders()->addHeaderLine("X-Cache: HIT");
                $this->getLogger()->error('CACHE', [0, "GET", $e->getMessage(), $event->getRequest()->getUri()]);
                return;
            }
        }, $priority);
        $events->attach(MvcEvent::EVENT_FINISH, function (MvcEvent $event) use ($cache) {
            if ($event->getRequest() instanceof \Zend\Console\Request) {
                return;
            }

            try {
                $storageKey = $this->storageKey($event->getRequest());
                if ($event->getRequest()->getMethod() === HttpRequest::METHOD_GET
                    && ! $cache->hasItem($storageKey)
                    && $event->getResponse()->isSuccess()
                ) {
                    $event->getResponse()->getHeaders()->addHeaderLine("X-Cache: MISS");
                    $cache->addItem($storageKey, serialize($event->getResult()));
                }
            } catch (\Exception $e) {
                $event->getResponse()->getHeaders()->addHeaderLine("X-Cache: MISS");
                return;
            }
        }, $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        // TODO: Implement detach() method.
    }

    /**
     * @param \Zend\Cache\Storage\StorageInterface $storage
     * @return $this
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    private function storageKey(PhpRequest $request)
    {
        $uri = $request->getUri();
        $query = $uri->getQueryAsArray();
        ksort($query);

        $range = $request->getHeaders()->has('Range')
            ? $request->getHeaders()->get('Range')->getFieldValue()
            : '';
        return md5($uri->getPath() . $range . json_encode($query));
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
