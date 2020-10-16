<?php
namespace AlthingiTest\Utils;

use Althingi\Utils\RequestCacheHandler;
use PHPUnit\Framework\TestCase;
use Laminas\Cache\Storage\Adapter\BlackHole;
use Laminas\Http\Headers;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Http\Request as HttpRequest;

class RequestCacheHandlerTest extends TestCase
{
    /**
     * MvcEvent::EVENT_ROUTE
     * GET request
     * Cache full
     */
    public function testMvcEventEventRouteHit()
    {
        $request = (new Request())->setMethod(HttpRequest::METHOD_GET);
        $response = new Response();
        $response->setHeaders(new Headers());

        $eventManager = new \Laminas\EventManager\EventManager();
        $application = $this->getEmptyApplication();
        $application->setRequest($request)
            ->setResponse($response)
            ->setEventManager($eventManager);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setResponse($response);
        $event->setApplication($application);
        $event->setName(MvcEvent::EVENT_ROUTE);

        (new RequestCacheHandler())->setStorage($this->getPopulatedCache())->attach($eventManager);

        $eventManager->triggerEvent($event);

        $this->assertTrue($event->getResponse()->getHeaders()->has('X-Cache'));
        $this->assertEquals('HIT', $event->getResponse()->getHeaders()->get('X-Cache')->getFieldValue());
    }
    /**
     * MvcEvent::EVENT_ROUTE
     * GET request
     * Cache empty
     */
    public function testMvcEventEventRouteMiss()
    {
        $request = (new Request())->setMethod(HttpRequest::METHOD_GET);
        $response = new Response();
        $response->setHeaders(new Headers());

        $eventManager = new \Laminas\EventManager\EventManager();
        $application = $this->getEmptyApplication();
        $application->setRequest($request)
            ->setResponse($response)
            ->setEventManager($eventManager);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setResponse($response);
        $event->setApplication($application);
        $event->setName(MvcEvent::EVENT_ROUTE);

        (new RequestCacheHandler())->setStorage($this->getEmptyCache())->attach($eventManager);

        $eventManager->triggerEvent($event);

        $this->assertFalse($event->getResponse()->getHeaders()->has('X-Cache'));
    }

    /**
     * MvcEvent::EVENT_FINISH
     * GET request
     * Cache empty
     * HTTP status code 200
     */
    public function testMvcEventEventFinishMiss()
    {
        $request = (new Request())->setMethod(HttpRequest::METHOD_GET);
        $response = new Response();
        $response->setHeaders(new Headers());

        $eventManager = new \Laminas\EventManager\EventManager();
        $application = $this->getEmptyApplication();
        $application->setRequest($request)
            ->setResponse($response)
            ->setEventManager($eventManager);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setResponse($response);
        $event->setApplication($application);
        $event->setName(MvcEvent::EVENT_FINISH);

        (new RequestCacheHandler())->setStorage($this->getEmptyCache())->attach($eventManager);

        $eventManager->triggerEvent($event);

        $this->assertTrue($event->getResponse()->getHeaders()->has('X-Cache'));
        $this->assertEquals('MISS', $event->getResponse()->getHeaders()->get('X-Cache')->getFieldValue());
    }

    /**
     * MvcEvent::EVENT_FINISH
     * GET request
     * Cache full
     * HTTP status code 200
     */
    public function testMvcEventEventFinishInCache()
    {
        $request = (new Request())->setMethod(HttpRequest::METHOD_GET);
        $response = new Response();
        $response->setHeaders(new Headers());

        $eventManager = new \Laminas\EventManager\EventManager();
        $application = $this->getEmptyApplication();
        $application->setRequest($request)
            ->setResponse($response)
            ->setEventManager($eventManager);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setResponse($response);
        $event->setApplication($application);
        $event->setName(MvcEvent::EVENT_FINISH);

        (new RequestCacheHandler())->setStorage($this->getPopulatedCache())->attach($eventManager);

        $eventManager->triggerEvent($event);

        $this->assertFalse($event->getResponse()->getHeaders()->has('X-Cache'));
    }

    /**
     * MvcEvent::EVENT_FINISH
     * GET request
     * Cache empty
     * HTTP status code 500
     */
    public function testMvcEventEventFinishErrorInRequest()
    {
        $request = (new Request())->setMethod(HttpRequest::METHOD_GET);
        $response = new Response();
        $response->setStatusCode(500);
        $response->setHeaders(new Headers());

        $eventManager = new \Laminas\EventManager\EventManager();
        $application = $this->getEmptyApplication();
        $application->setRequest($request)
            ->setResponse($response)
            ->setEventManager($eventManager);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setResponse($response);
        $event->setApplication($application);
        $event->setName(MvcEvent::EVENT_FINISH);

        (new RequestCacheHandler())->setStorage($this->getEmptyCache())->attach($eventManager);

        $eventManager->triggerEvent($event);

        $this->assertFalse($event->getResponse()->getHeaders()->has('X-Cache'));
    }

    private function getPopulatedCache()
    {
        return new class extends BlackHole
        {
            public function hasItem($key)
            {
                return true;
            }

            public function getItem($key, &$success = null, &$casToken = null)
            {
                return serialize(new \Rend\View\Model\EmptyModel());
            }
        };
    }

    private function getEmptyCache()
    {
        return new class extends BlackHole
        {
            public function hasItem($key)
            {
                return false;
            }

            public function getItem($key, &$success = null, &$casToken = null)
            {
                return null;
            }
        };
    }

    private function getEmptyApplication()
    {
        return new class implements ApplicationInterface
        {
            private $request;
            private $response;
            private $eventManager;

            public function getServiceManager()
            {
                // TODO: Implement getServiceManager() method.
            }

            public function setRequest($request)
            {
                $this->request = $request;
                return $this;
            }

            public function getRequest()
            {
                return $this->request;
            }

            public function setResponse($response)
            {
                $this->response = $response;
                return $this;
            }

            public function getResponse()
            {
                return $this->response;
            }

            public function run()
            {
                // TODO: Implement run() method.
            }

            public function setEventManager($manager)
            {
                $this->eventManager = $manager;
                return $this;
            }

            public function getEventManager()
            {
                return $this->eventManager;
            }
        };
    }
}
