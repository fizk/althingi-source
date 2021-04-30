<?php
namespace Althingi\Injector;

use Althingi\Utils\MessageBrokerInterface;

interface MessageBrokerAwareInterface
{
    public function setMessageBroker(MessageBrokerInterface $connection);

    public function getMessageBroker(): MessageBrokerInterface;
}
