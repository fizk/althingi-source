<?php

namespace Althingi;

use Althingi\Utils\RequestCacheHandler;
use Althingi\Utils\RequestErrorHandler;
use Psr\Log\LoggerInterface;
use Zend\Mvc\MvcEvent;
use Rend\Event\ShutdownErrorHandler;
use Zend\Cache\Storage\StorageInterface;

class Module
{
    const VERSION = '3.0.3-dev';

    public function onBootstrap(MvcEvent $event)
    {
        register_shutdown_function(function () {
            if ($error = error_get_last()) {
                $message = explode(PHP_EOL, $error['message']);
                $errorArray = json_encode($message);
                $firstMessage = addslashes(array_shift($message));
                file_put_contents(
                    getenv('LOG_PATH') ? : 'php://stdout',
                    "[".date('Y-m-d H:i:s')."] althingi-api.ERROR: ".
                    "ERROR [500, \"ERROR\", \"{$firstMessage}\", {$errorArray}]" .
                    " {\"memory_usage\":0,\"memory_peak_usage\":0}". PHP_EOL,
                    FILE_APPEND
                );
                exit(1);
            }
        });

        $eventManager = $event->getApplication()
            ->getEventManager();
        $storageInterface = $event->getApplication()
            ->getServiceManager()
            ->get(StorageInterface::class);
        $loggerInterface = $event->getApplication()
            ->getServiceManager()
            ->get(LoggerInterface::class);

        (new RequestErrorHandler())
            ->attach($eventManager, 1);

        (new RequestCacheHandler())
            ->setStorage($storageInterface)
            ->setLogger($loggerInterface)
            ->attach($eventManager, 2);

        $eventManager->attach(MvcEvent::EVENT_FINISH, function (MvcEvent $event) use ($loggerInterface) {
            if ($event->getResponse() instanceof \Zend\Console\Response) {
                return;
            }
            if ($event->getResponse()->getStatusCode() >= 400) {
                $loggerInterface->error('REQUEST', [
                    $event->getResponse()->getStatusCode(),
                    $event->getRequest()->getMethod(),
                    $event->getRequest()->getRequestUri(),
                    $event->getResponse()->getContent(), [
                        'request' => $event->getRequest()->getHeaders()->toString(),
                        'response' => $event->getResponse()->getHeaders()->toString()
                    ],
                ]);
            } else {
                $loggerInterface->info('REQUEST', [
                    $event->getResponse()->getStatusCode(),
                    $event->getRequest()->getMethod(),
                    $event->getRequest()->getRequestUri(), [
                        'request' => $event->getRequest()->getHeaders()->toString(),
                        'response' => $event->getResponse()->getHeaders()->toString()
                    ],
                ]);
            }
        }, 1);
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
