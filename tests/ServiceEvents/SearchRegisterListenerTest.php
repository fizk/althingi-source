<?php
/**
 * Created by PhpStorm.
 * User: einar.adalsteinsson
 * Date: 10/19/17
 * Time: 8:30 AM
 */

namespace Althingi\ServiceEvents;

use Althingi\Model\Speech as SpeechModel;
use Althingi\Hydrator\Speech as SpeechHydrator;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\EventManager;

class SearchRegisterListenerTest extends PHPUnit_Framework_TestCase
{
    public function testTrue()
    {
        $eventManager = new EventManager();


        $event = new AddEvent(new SpeechModel(), new SpeechHydrator());

        $listener = new ServiceEventsListener('');
        $listener->attach($eventManager);

        $eventManager->trigger($event);
    }
}
