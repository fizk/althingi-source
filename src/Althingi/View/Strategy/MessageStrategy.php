<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 26/04/15
 * Time: 9:01 PM
 */

namespace Althingi\View\Strategy;

use Althingi\View\Model\EmptyModel;
use Althingi\View\Model\ModelInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Renderer\RendererInterface;
use Zend\View\ViewEvent;

/**
 * Class MessageStrategy
 * @package Restvisi\View\Strategy
 */
class MessageStrategy extends AbstractListenerAggregate
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @var array
     */
    protected $listeners = array();

    /**
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'));
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'));
    }

    /**
     * @param ViewEvent $e
     * @return void|RendererInterface
     */
    public function selectRenderer(ViewEvent $e)
    {
        if (!$e->getModel() instanceof ModelInterface) {
            return;
        }
        return $this->renderer;
    }

    /**
     * @param ViewEvent $e
     */
    public function injectResponse(ViewEvent $e)
    {
        if (!$e->getModel() instanceof ModelInterface) {
            return;
        }

        $result   = $e->getResult();

        $model = $e->getModel();
        /** @var $model \Zend\View\Model\ModelInterface */

        // Populate response
        $response = $e->getResponse();
        /** @var $response \Zend\Http\PhpEnvironment\Response */
        $response->setContent($result);

        if (get_class($model) == 'Althingi\View\Model\EmptyModel') {//FIXME
            $response->setContent('');
        }
        $response->setStatusCode($model->getStatus());
        $headers = $response->getHeaders();
        foreach ($model->getOptions() as $key => $value) {
            $headers->addHeaderLine($key, $value);
        }
        $headers->addHeaderLine('content-type', 'application/json; charset=utf-8');
    }

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
}
